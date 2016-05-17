<?php
namespace groupcash\socialhours\app;

use groupcash\php\Groupcash;
use groupcash\php\model\signing\Algorithm;
use groupcash\socialhours\AuthorizeCreditor;
use groupcash\socialhours\CheckBalance;
use groupcash\socialhours\CheckCreditedHours;
use groupcash\socialhours\CreateAccount;
use groupcash\socialhours\CreditHours;
use groupcash\socialhours\events\TokenGenerated;
use groupcash\socialhours\LogIn;
use groupcash\socialhours\LogOut;
use groupcash\socialhours\model\PostOffice;
use groupcash\socialhours\model\SocialHours;
use groupcash\socialhours\model\Token;
use groupcash\socialhours\projections\Balance;
use groupcash\socialhours\projections\CreditedHours;
use groupcash\socialhours\RegisterOrganisation;
use rtens\domin\delivery\web\adapters\curir\root\IndexResource;
use rtens\domin\delivery\web\fields\AdapterField;
use rtens\domin\delivery\web\fields\StringField;
use rtens\domin\delivery\web\WebApplication;
use rtens\domin\Parameter;
use rtens\domin\reflection\GenericObjectAction;
use watoki\curir\WebDelivery;
use watoki\karma\implementations\aggregates\ObjectAggregateFactory;
use watoki\karma\implementations\GenericApplication;
use watoki\karma\implementations\listeners\ObjectListener;
use watoki\karma\implementations\projections\ObjectProjectionFactory;
use watoki\karma\stores\EventStore;
use watoki\reflect\type\ClassType;

class Launcher {

    /** @var \watoki\karma\Application */
    public $application;

    private static $actionGroups = [
        'Reporting' => [
            CheckBalance::class,
            CheckCreditedHours::class
        ],
        'Administration' => [
            CreateAccount::class,
            RegisterOrganisation::class,
            AuthorizeCreditor::class,
            CreditHours::class
        ],
        'Access' => [
            LogIn::class,
            LogOut::class
        ]
    ];

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
            $this->addActions($app);

            $app->fields->add((new AdapterField(new StringField()))
                ->setHandles(function (Parameter $parameter) {
                    return $parameter->getType() == new ClassType(Token::class);
                })
                ->setTransformParameter(function (Parameter $parameter) {
                    return $parameter->withType(new ClassType(Token::class));
                })
                ->setAfterInflate(function ($value) {
                    return new Token($value);
                }));
        }, WebDelivery::init()));
    }

    private function addActions(WebApplication $app) {
        foreach ($this->findActions() as $class) {
            $this->addAction($app, $class);
        }
        foreach (self::$actionGroups as $group => $actions) {
            foreach ($actions as $action) {
                $app->groups->put($this->makeActionId($action), $group);
            }
        }
    }

    private function addAction(WebApplication $app, $class) {
        $id = $this->makeActionId($class);
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
                (string)$e->getToken()
            );
        }, TokenGenerated::class);
    }

    private function findActions() {
        $classes = get_declared_classes();
        foreach (glob(__DIR__ . '/../*.php') as $file) {
            /** @noinspection PhpIncludeInspection */
            include_once $file;
        }
        return array_diff(get_declared_classes(), $classes);
    }

    private function makeActionId($class) {
        return (new \ReflectionClass($class))->getShortName();
    }
}