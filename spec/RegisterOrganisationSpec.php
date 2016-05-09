<?php
namespace spec\groupcash\socialhours;

use groupcash\php\model\signing\Binary;
use groupcash\socialhours\CreateAccount;
use groupcash\socialhours\events\OrganisationRegistered;
use groupcash\socialhours\model\Time;
use groupcash\socialhours\RegisterOrganisation;

class RegisterOrganisationSpec extends SocialHoursSpecification {

    function before() {
        $this->algorithm->nextKey = 'social';
        Time::freeze(new \DateTimeImmutable('2011-12-13 14:15:16 UTC'));
    }

    function success() {
        $this->when(new RegisterOrganisation('Foo ', 'foo@bar.com'));
        $this->then(new OrganisationRegistered(
            new \DateTimeImmutable('2011-12-13 14:15:16 UTC'),
            'Foo',
            'foo@bar.com',
            new Binary('social'),
            new Binary('social key')
        ));
    }

    function emailTaken() {
        $this->when(new RegisterOrganisation('Foo', 'foo@bar.com'));
        $this->when->tryTo(new RegisterOrganisation('Bar', 'foo@bar.com'));
        $this->then->shouldFail('An organisation with this email address is already registered.');
    }

    function emailTakenByAccount() {
        $this->when(new CreateAccount('foo@bar.com'));
        $this->when->tryTo(new RegisterOrganisation('Bar', 'foo@bar.com'));
        $this->then->shouldFail('An account with this email address was already created.');
    }

    function ignoreEmailCaseAndSpaces() {
        $this->when(new RegisterOrganisation('Foo', 'foo@BAR.com '));
        $this->when->tryTo(new RegisterOrganisation('Bar', ' FOO@bar.com'));
        $this->then->shouldFail('An organisation with this email address is already registered.');
    }

    function nameTaken() {
        $this->when(new RegisterOrganisation('Foo', 'foo@bar.com '));
        $this->when->tryTo(new RegisterOrganisation('Foo', ' foo@baz.com'));
        $this->then->shouldFail('An organisation with this name is already registered.');
    }

    function ignoreNameCaseAndSpaces() {
        $this->when(new RegisterOrganisation('FOO ', 'foo@bar.com '));
        $this->when->tryTo(new RegisterOrganisation(' foo', ' foo@baz.com'));
        $this->then->shouldFail('An organisation with this name is already registered.');
    }
}