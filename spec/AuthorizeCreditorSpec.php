<?php
namespace spec\groupcash\socialhours;

use groupcash\php\model\signing\Binary;
use groupcash\socialhours\AuthorizeCreditor;
use groupcash\socialhours\events\AccountCreated;
use groupcash\socialhours\events\CreditorAuthorized;
use groupcash\socialhours\events\OrganisationRegistered;
use groupcash\socialhours\events\TokenGenerated;
use groupcash\socialhours\model\Time;

class AuthorizeCreditorSpec extends SocialHoursSpecification {

    function invalidToken() {
        $this->when->tryTo(new AuthorizeCreditor('wrong token', 'creditor@foo'));
        $this->then->shouldFail('Invalid token.');
    }

    function notAdministrator() {
        $this->given(new TokenGenerated(Time::now(), 'that token', 'notAdmin@foo'));
        $this->when->tryTo(new AuthorizeCreditor('that token', 'creditor@foo'));
        $this->then->shouldFail('Only administrators of organisations can authorize creditors.');
    }

    function accountNotExisting() {
        $this->given(new OrganisationRegistered(Time::now(), 'Foo', 'foo@bar', new Binary('foo'), new Binary('foo key')));
        $this->given(new TokenGenerated(Time::now(), 'that token', 'foo@bar'));
        $this->when->tryTo(new AuthorizeCreditor('that token', 'creditor@foo'));
        $this->then->shouldFail('No account was created with this email address.');
    }

    function cannotAuthorizeAdministrator() {
        $this->given(new OrganisationRegistered(Time::now(), 'Foo', 'foo@bar', new Binary('foo'), new Binary('foo key')));
        $this->given(new TokenGenerated(Time::now(), 'that token', 'foo@bar'));
        $this->given(new OrganisationRegistered(Time::now(), 'Bar', 'creditor@foo', new Binary('bar'), new Binary('bar key')));
        $this->when->tryTo(new AuthorizeCreditor('that token', 'creditor@foo'));
        $this->then->shouldFail('No account was created with this email address.');
    }

    function success() {
        $this->given(new OrganisationRegistered(Time::now(), 'Foo', 'foo@bar', new Binary('foo'), new Binary('foo key')));
        $this->given(new TokenGenerated(Time::now(), 'that token', 'foo@bar'));
        $this->given(new AccountCreated(Time::now(), 'creditor@foo', new Binary('creditor'), new Binary('creditor key')));
        $this->when(new AuthorizeCreditor('that token', 'creditor@foo'));
        $this->then(new CreditorAuthorized(Time::now(), 'Foo', 'creditor@foo'));
    }
}