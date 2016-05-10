<?php
namespace spec\groupcash\socialhours;

use groupcash\php\model\signing\Binary;
use groupcash\socialhours\CreditHours;
use groupcash\socialhours\events\AccountCreated;
use groupcash\socialhours\events\CreditorAuthorized;
use groupcash\socialhours\events\HoursCredited;
use groupcash\socialhours\events\OrganisationRegistered;
use groupcash\socialhours\events\TokenGenerated;
use groupcash\socialhours\model\Time;

class CreditHoursSpec extends SocialHoursSpecification {

    function invalidToken() {
        $this->when->tryTo(new CreditHours('not a token', 'Foo', 'foo@bar', 'Good work', 1));
        $this->then->shouldFail('Invalid token.');
    }

    function notAuthorized() {
        $this->given(new CreditorAuthorized(Time::now(), 'Foo', 'other@foo'));
        $this->given(new TokenGenerated(Time::now(), 'my token', 'bar@foo'));
        $this->when->tryTo(new CreditHours('my token', 'Foo', 'foo@bar', 'Good work', 1));
        $this->then->shouldFail('Only creditors and administrators can credit hours.');
    }

    function authorizedByOtherOrganisation() {
        $this->given(new CreditorAuthorized(Time::now(), 'Bar', 'creditor@bar'));
        $this->given(new TokenGenerated(Time::now(), 'my token', 'creditor@bar'));
        $this->when->tryTo(new CreditHours('my token', 'Foo', 'volunteer@foo', 'Good work', 1));
        $this->then->shouldFail('Only creditors and administrators can credit hours.');
    }

    function nonExistingAccount() {
        $this->given(new CreditorAuthorized(Time::now(), 'Foo', 'creditor@foo'));
        $this->given(new TokenGenerated(Time::now(), 'my token', 'creditor@foo'));
        $this->when->tryTo(new CreditHours('my token', 'Foo', 'volunteer@foo', 'Good work', 1));
        $this->then->shouldFail('No account was created with this email address.');
    }

    function success() {
        $this->given(new CreditorAuthorized(Time::now(), 'Foo', 'creditor@foo'));
        $this->given(new TokenGenerated(Time::now(), 'my token', 'creditor@foo'));
        $this->given(new AccountCreated(Time::now(), 'volunteer@foo', new Binary('volunteer'), new Binary('volunteer key')));
        $this->when(new CreditHours('my token', 'Foo', 'volunteer@foo', 'Good work', 1));
        $this->then(new HoursCredited(Time::now(), 'Foo', 'volunteer@foo', 'Good work', 1));
    }

    function creditAsAdministrator() {
        $this->given(new OrganisationRegistered(Time::now(), 'Foo', 'admin@foo', new Binary('foo'), new Binary('foo key')));
        $this->given(new TokenGenerated(Time::now(), 'my token', 'admin@foo'));
        $this->given(new AccountCreated(Time::now(), 'volunteer@foo', new Binary('volunteer'), new Binary('volunteer key')));
        $this->when(new CreditHours('my token', 'Foo', 'volunteer@foo', 'Good work', 1));
        $this->then(new HoursCredited(Time::now(), 'Foo', 'volunteer@foo', 'Good work', 1));
    }
}