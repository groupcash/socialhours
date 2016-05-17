<?php
namespace spec\groupcash\socialhours;

use groupcash\php\model\signing\Binary;
use groupcash\socialhours\AuthorizeCreditor;
use groupcash\socialhours\events\AccountCreated;
use groupcash\socialhours\events\CreditorAuthorized;
use groupcash\socialhours\events\OrganisationRegistered;
use groupcash\socialhours\events\TokenGenerated;
use groupcash\socialhours\model\AccountIdentifier;
use groupcash\socialhours\model\Time;
use groupcash\socialhours\model\Token;

class AuthorizeCreditorSpec extends SocialHoursSpecification {

    function invalidToken() {
        $this->when->tryTo(new AuthorizeCreditor(new Token('wrong token'), new AccountIdentifier('creditor@foo')));
        $this->then->shouldFail('Invalid token.');
    }

    function notAdministrator() {
        $this->given(new TokenGenerated(Time::now(), new Token('that token'), new Binary('not admin'), 'email'));
        $this->when->tryTo(new AuthorizeCreditor(new Token('that token'), new AccountIdentifier('creditor')));
        $this->then->shouldFail('Only administrators of organisations can authorize creditors.');
    }

    function accountNotExisting() {
        $this->given(new OrganisationRegistered(Time::now(), new Binary('foo'), new Binary('foo key'), 'foo@bar', 'Foo'));
        $this->given(new TokenGenerated(Time::now(), new Token('that token'), new Binary('foo'), 'email'));
        $this->when->tryTo(new AuthorizeCreditor(new Token('that token'), new AccountIdentifier('creditor')));
        $this->then->shouldFail('No account was created with this email address.');
    }

    function cannotAuthorizeAdministrator() {
        $this->given(new OrganisationRegistered(Time::now(), new Binary('foo'), new Binary('foo key'), 'foo', 'Foo'));
        $this->given(new OrganisationRegistered(Time::now(), new Binary('bar'), new Binary('bar key'), 'bar', 'Bar'));
        $this->given(new TokenGenerated(Time::now(), new Token('that token'), new Binary('foo'), 'email'));
        $this->when->tryTo(new AuthorizeCreditor(new Token('that token'), new AccountIdentifier('bar')));
        $this->then->shouldFail('Cannot authorize administrators as creditors.');
    }

    function success() {
        $this->given(new OrganisationRegistered(Time::now(), new Binary('foo'), new Binary('foo key'), 'foo@bar', 'Foo'));
        $this->given(new TokenGenerated(Time::now(), new Token('that token'), new Binary('foo'), 'email'));
        $this->given(new AccountCreated(Time::now(), new Binary('creditor'), new Binary('creditor'), 'creditor@foo'));
        $this->when(new AuthorizeCreditor(new Token('that token'), new AccountIdentifier('creditor@foo')));
        $this->then(new CreditorAuthorized(Time::now(), new Binary('foo'), new Binary('creditor')));
    }
}