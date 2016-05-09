<?php
namespace spec\groupcash\socialhours;

use groupcash\php\algorithms\FakeAlgorithm;
use groupcash\php\Groupcash;
use groupcash\php\model\signing\Binary;
use groupcash\socialhours\CreateAccount;
use groupcash\socialhours\events\AccountCreated;
use groupcash\socialhours\model\SocialHours;
use groupcash\socialhours\model\Time;
use watoki\karma\implementations\aggregates\ObjectAggregateFactory;
use watoki\karma\implementations\GenericApplication;
use watoki\karma\stores\EventStore;
use watoki\karma\testing\Specification;

class CreateAccountSpec extends Specification {

    /** @var FakeAlgorithm */
    private $algorithm;

    public function __construct() {
        $this->algorithm = new FakeAlgorithm();

        parent::__construct(function (EventStore $store) {
            return new GenericApplication($store, new ObjectAggregateFactory(function () {
                return new SocialHours(new Groupcash($this->algorithm));
            }));
        });
    }

    function onlyEmail() {
        $this->algorithm->nextKey = 'foo';
        Time::freeze(new \DateTimeImmutable('2011-12-13 14:15:16 UTC'));

        $this->when(new CreateAccount('foo@bar.com'));
        $this->then(new AccountCreated(
            new \DateTimeImmutable('2011-12-13 14:15:16 UTC'),
            'foo@bar.com',
            new Binary('foo'),
            new Binary('foo key')
        ));
    }
}