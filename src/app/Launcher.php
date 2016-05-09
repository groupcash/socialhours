<?php
namespace groupcash\socialhours\app;

use groupcash\php\Groupcash;
use groupcash\php\model\signing\Algorithm;
use groupcash\socialhours\CreateAccount;
use groupcash\socialhours\events\TokenGenerated;
use groupcash\socialhours\LogIn;
use groupcash\socialhours\LogOut;
use groupcash\socialhours\model\PostOffice;
use groupcash\socialhours\model\SocialHours;
use groupcash\socialhours\RegisterOrganisation;
use rtens\domin\delivery\web\adapters\curir\root\IndexResource;
use rtens\domin\delivery\web\WebApplication;
use rtens\domin\reflection\GenericObjectAction;
use watoki\curir\WebDelivery;
use watoki\karma\implementations\aggregates\ObjectAggregateFactory;
use watoki\karma\implementations\GenericApplication;
use watoki\karma\implementations\listeners\ObjectListener;
use watoki\karma\stores\EventStore;

class Launcher {

    /** @var \watoki\karma\Application */
    public $application;

    public function __construct(EventStore $store, Algorithm $algorithm, PostOffice $postOffice) {
        $this->application = (new GenericApplication($store,
            new ObjectAggregateFactory(function () use ($algorithm, $postOffice) {
                return new SocialHours(new Groupcash($algorithm));
            })
        ))->addListener($this->tokenGeneratedListener($postOffice));
    }

    public function run() {
        WebDelivery::quickResponse(IndexResource::class, WebApplication::init(function (WebApplication $app) {
            $this->addAction($app, LogIn::class);
            $this->addAction($app, CreateAccount::class);
            $this->addAction($app, RegisterOrganisation::class);
            $this->addAction($app, LogOut::class);
        }, WebDelivery::init()));
    }

    private function addAction(WebApplication $app, $class) {
        $id = (new \ReflectionClass($class))->getShortName();
        $execute = function ($action) {
            $this->application->handle($action);
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
}