<?php
namespace groupcash\socialhours\app;

use groupcash\php\Groupcash;
use groupcash\php\model\signing\Algorithm;
use groupcash\socialhours\CheckBalance;
use groupcash\socialhours\CheckCreditedHours;
use groupcash\socialhours\events\TokenGenerated;
use groupcash\socialhours\model\PostOffice;
use groupcash\socialhours\model\SocialHours;
use groupcash\socialhours\projections\Balance;
use groupcash\socialhours\projections\CreditedHours;
use rtens\domin\delivery\web\adapters\curir\root\IndexResource;
use rtens\domin\delivery\web\WebApplication;
use rtens\domin\reflection\GenericObjectAction;
use watoki\curir\WebDelivery;
use watoki\karma\implementations\aggregates\ObjectAggregateFactory;
use watoki\karma\implementations\GenericApplication;
use watoki\karma\implementations\listeners\ObjectListener;
use watoki\karma\implementations\projections\ObjectProjectionFactory;
use watoki\karma\stores\EventStore;

class Launcher {

    /** @var \watoki\karma\Application */
    public $application;

    public function __construct(EventStore $store, Algorithm $algorithm, PostOffice $postOffice) {
        $this->application = (new GenericApplication($store,
            new ObjectAggregateFactory(function () use ($algorithm, $postOffice) {
                return new SocialHours(new Groupcash($algorithm));
            }),
            new ObjectProjectionFactory(function ($query) {
                if ($query instanceof CheckBalance) {
                    return new Balance($query);
                } else if ($query instanceof CheckCreditedHours) {
                    return new CreditedHours($query);
                }

                throw new \Exception('Unknown query.');
            })))
            ->setCommandCondition(function ($command) {
                return substr((new \ReflectionClass($command))->getShortName(), 0, 5) !== 'Check';
            })
            ->addListener($this->tokenGeneratedListener($postOffice));
    }

    public function run() {
        WebDelivery::quickResponse(IndexResource::class, WebApplication::init(function (WebApplication $app) {
            $app->setNameAndBrand('Social Hours');
            foreach ($this->findActions() as $class) {
                $this->addAction($app, $class);
            }
        }, WebDelivery::init()));
    }

    private function addAction(WebApplication $app, $class) {
        $id = (new \ReflectionClass($class))->getShortName();
        $execute = function ($action) {
            return $this->application->handle($action);
        };

        return $app->actions->add($id, new GenericObjectAction($class, $app->types, $app->parser, $execute));
    }

    private function tokenGeneratedListener(PostOffice $postOffice) {
        return new ObjectListener(function (TokenGenerated $e) use ($postOffice) {
            $postOffice->send(
                $e->getEmail(),
                'Log-in token',
                $e->getToken()
            );
        }, TokenGenerated::class);
    }

    private function findActions() {
        $classes = get_declared_classes();
        foreach (glob(__DIR__ . '/../*.php') as $file) {
            include_once $file;
        }
        return array_diff(get_declared_classes(), $classes);
    }
}