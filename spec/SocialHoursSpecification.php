<?php
namespace spec\groupcash\socialhours;

use groupcash\php\algorithms\FakeAlgorithm;
use groupcash\php\Groupcash;
use groupcash\socialhours\model\SocialHours;
use watoki\karma\implementations\aggregates\ObjectAggregateFactory;
use watoki\karma\implementations\GenericApplication;
use watoki\karma\stores\EventStore;
use watoki\karma\testing\Specification;

class SocialHoursSpecification extends Specification {

    /** @var FakeAlgorithm */
    protected $algorithm;

    public function __construct() {
        $this->algorithm = new FakeAlgorithm();

        parent::__construct(function (EventStore $store) {
            return new GenericApplication($store, new ObjectAggregateFactory(function () {
                return new SocialHours(new Groupcash($this->algorithm));
            }));
        });
    }
}