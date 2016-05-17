<?php
namespace spec\groupcash\socialhours;

use groupcash\php\model\signing\Binary;
use groupcash\socialhours\CreditHours;
use groupcash\socialhours\events\AccountCreated;
use groupcash\socialhours\events\CreditorAuthorized;
use groupcash\socialhours\events\HoursCredited;
use groupcash\socialhours\events\OrganisationRegistered;
use groupcash\socialhours\events\TokenGenerated;
use groupcash\socialhours\model\AccountIdentifier;
use groupcash\socialhours\model\OrganisationIdentifier;
use groupcash\socialhours\model\Time;
use groupcash\socialhours\model\Token;

class CreditHoursSpec extends SocialHoursSpecification {

    function invalidToken() {
        $this->when->tryTo(new CreditHours(new Token('not a token'), new OrganisationIdentifier('foo'), new AccountIdentifier('bar'), 'Good work', 1));
        $this->then->shouldFail('Invalid token.');
    }

    function notAuthorized() {
        $this->given(new OrganisationRegistered(Time::now(), new Binary('foo'), new Binary('foo key'), 'admin', 'foo'));
        $this->given(new TokenGenerated(Time::now(), new Token('my token'), new Binary('baz'), 'email'));
        $this->when->tryTo(new CreditHours(new Token('my token'), new OrganisationIdentifier('foo'), new AccountIdentifier('bar'), 'Good work', 1));
        $this->then->shouldFail('Only creditors and administrators can credit hours.');
    }

    function authorizedByOtherOrganisation() {
        $this->given(new OrganisationRegistered(Time::now(), new Binary('foo'), new Binary('foo key'), 'admin', 'foo'));
        $this->given(new CreditorAuthorized(Time::now(), new Binary('fos'), new Binary('baz')));
        $this->given(new TokenGenerated(Time::now(), new Token('my token'), new Binary('baz'), 'email'));
        $this->when->tryTo(new CreditHours(new Token('my token'), new OrganisationIdentifier('foo'), new AccountIdentifier('bar'), 'Good work', 1));
        $this->then->shouldFail('Only creditors and administrators can credit hours.');
    }

    function success() {
        $this->given(new OrganisationRegistered(Time::now(), new Binary('foo'), new Binary('foo key'), 'admin', 'foo'));
        $this->given(new CreditorAuthorized(Time::now(), new Binary('foo'), new Binary('baz')));
        $this->given(new TokenGenerated(Time::now(), new Token('my token'), new Binary('baz'), 'email'));
        $this->given(new AccountCreated(Time::now(), new Binary('bar'), new Binary('bar key'), 'bar'));
        $this->when(new CreditHours(new Token('my token'), new OrganisationIdentifier('foo'), new AccountIdentifier('bar'), 'Good work', 1));
        $this->then(new HoursCredited(Time::now(), new Binary('baz'), new Binary('foo'), new Binary('bar'), 'Good work', 1));
    }

    function creditAsAdministrator() {
        $this->given(new OrganisationRegistered(Time::now(), new Binary('foo'), new Binary('foo key'), 'admin', 'foo'));
        $this->given(new TokenGenerated(Time::now(), new Token('my token'), new Binary('foo'), 'email'));
        $this->given(new AccountCreated(Time::now(), new Binary('bar'), new Binary('bar key'), 'bar'));
        $this->when(new CreditHours(new Token('my token'), new OrganisationIdentifier('foo'), new AccountIdentifier('bar'), 'Good work', 1));
        $this->then(new HoursCredited(Time::now(), new Binary('foo'), new Binary('foo'), new Binary('bar'), 'Good work', 1));
    }

    function nonExistingAccount() {
        $this->given(new OrganisationRegistered(Time::now(), new Binary('foo'), new Binary('foo key'), 'admin', 'foo'));
        $this->given(new CreditorAuthorized(Time::now(), new Binary('foo'), new Binary('baz')));
        $this->given(new TokenGenerated(Time::now(), new Token('my token'), new Binary('baz'), 'email'));
        $this->when->tryTo(new CreditHours(new Token('my token'), new OrganisationIdentifier('foo'), new AccountIdentifier('bar'), 'Good work', 1));
        $this->then->shouldFail('No account was created with this email address.');
    }
}