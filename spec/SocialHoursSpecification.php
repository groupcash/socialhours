<?php
namespace spec\groupcash\socialhours;

use groupcash\php\algorithms\FakeAlgorithm;
use groupcash\php\Groupcash;
use groupcash\socialhours\events\TokenGenerated;
use groupcash\socialhours\model\PostOffice;
use groupcash\socialhours\model\SocialHours;
use groupcash\socialhours\model\Time;
use rtens\mockster\Mockster;
use watoki\karma\implementations\aggregates\ObjectAggregateFactory;
use watoki\karma\implementations\GenericApplication;
use watoki\karma\implementations\listeners\ObjectListener;
use watoki\karma\stores\EventStore;
use watoki\karma\testing\Specification;

class SocialHoursSpecification extends Specification {

    /** @var FakeAlgorithm */
    protected $algorithm;
    /** @var PostOffice */
    protected $postOffice;

    public function __construct() {
        $this->algorithm = new FakeAlgorithm();
        $this->postOffice = Mockster::of(PostOffice::class);

        Time::freeze();

        parent::__construct(function (EventStore $store) {
            return (new GenericApplication($store, new ObjectAggregateFactory(function () {
                return new SocialHours(new Groupcash($this->algorithm));
            })))
                ->addListener(new ObjectListener(function (TokenGenerated $e) {
                    Mockster::mock($this->postOffice)->send(
                        'SocialHours@groupcash.org',
                        $e->getEmail(),
                        'Log-in token',
                        $e->getToken()
                    );
                }, TokenGenerated::class));
        });
    }
}