<?php
namespace spec\groupcash\socialhours;

use groupcash\php\model\signing\Binary;
use groupcash\socialhours\CreateAccount;
use groupcash\socialhours\events\AccountCreated;
use groupcash\socialhours\model\Time;

class CreateAccountSpec extends SocialHoursSpecification {

    function before() {
        $this->algorithm->nextKey = 'foo';
        Time::freeze(new \DateTimeImmutable('2011-12-13 14:15:16 UTC'));
    }

    function onlyEmail() {
        $this->when(new CreateAccount('foo@bar.com'));
        $this->then(new AccountCreated(
            new \DateTimeImmutable('2011-12-13 14:15:16 UTC'),
            'foo@bar.com',
            new Binary('foo'),
            new Binary('foo key')
        ));
    }

    function emailAndName() {
        $this->when(new CreateAccount('foo@bar.com', 'Foo Bar'));
        $this->then->shouldMatchObject(AccountCreated::class, function (AccountCreated $accountCreated) {
            return $accountCreated->getName() == 'Foo Bar';
        });
    }
}