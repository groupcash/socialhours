<?php
namespace spec\groupcash\socialhours;

use groupcash\php\model\signing\Binary;
use groupcash\socialhours\CheckCreditedHours;
use groupcash\socialhours\events\CreditorAuthorized;
use groupcash\socialhours\events\HoursCredited;
use groupcash\socialhours\events\OrganisationRegistered;
use groupcash\socialhours\events\TokenDestroyed;
use groupcash\socialhours\events\TokenGenerated;
use groupcash\socialhours\model\Time;
use groupcash\socialhours\projections\CreditedHours;

class CheckCreditedHoursSpec extends SocialHoursSpecification {

    function invalidToken() {
        $this->when(new CheckCreditedHours('wrong token'));
        $this->then->returnShouldMatch(function (CreditedHours $p) {
            return $p->getHistory() == [] && $p->getTotalHours() == 0;
        });
    }

    function successAsCreditor() {
        $this->given(new HoursCredited(Time::now(), 'Foo', 'me@foo', 'v@foo', 'One', 30));
        $this->given(new CreditorAuthorized(Time::now(), 'Foo', 'c@foo'));
        $this->given(new TokenGenerated(Time::now(), 'my token', 'c@foo'));

        $this->when(new CheckCreditedHours('my token'));
        $this->then->returnShouldMatch(function (CreditedHours $p) {
            return $p->getHistory() == [
                new HoursCredited(Time::now(), 'Foo', 'me@foo', 'v@foo', 'One', 30)
            ];
        });
    }

    function sumHours() {
        $this->given(new HoursCredited(Time::now(), 'Foo', 'me@foo', 'v@foo', 'One', 30));
        $this->given(new HoursCredited(Time::now(), 'Foo', 'me@foo', 'v@foo', 'Two', 45));
        $this->given(new CreditorAuthorized(Time::now(), 'Foo', 'c@foo'));
        $this->given(new TokenGenerated(Time::now(), 'my token', 'c@foo'));

        $this->when(new CheckCreditedHours('my token'));
        $this->then->returnShouldMatch(function (CreditedHours $p) {
            return $p->getTotalHours() == 1.25;
        });
    }

    function notCreditor() {
        $this->given(new HoursCredited(Time::now(), 'Foo', 'me@foo', 'v@foo', 'One', 30));
        $this->given(new TokenGenerated(Time::now(), 'my token', 'c@foo'));

        $this->when(new CheckCreditedHours('my token'));
        $this->then->returnShouldMatch(function (CreditedHours $p) {
            return $p->getHistory() == [];
        });
    }

    function creditorOfManyOrganisations() {
        $this->given(new HoursCredited(Time::now(), 'Foo', 'me@foo', 'v@foo', 'One', 30));
        $this->given(new HoursCredited(Time::now(), 'Bar', 'you@foo', 'w@foo', 'Two', 45));

        $this->given(new CreditorAuthorized(Time::now(), 'Foo', 'c@foo'));
        $this->given(new CreditorAuthorized(Time::now(), 'Bar', 'c@foo'));

        $this->given(new TokenGenerated(Time::now(), 'my token', 'c@foo'));

        $this->when(new CheckCreditedHours('my token'));
        $this->then->returnShouldMatch(function (CreditedHours $p) {
            return $p->getHistory() == [
                new HoursCredited(Time::now(), 'Foo', 'me@foo', 'v@foo', 'One', 30),
                new HoursCredited(Time::now(), 'Bar', 'you@foo', 'w@foo', 'Two', 45)
            ];
        });
    }

    function creditorOfSingleOrganisations() {
        $this->given(new HoursCredited(Time::now(), 'Foo', 'me@foo', 'v@foo', 'One', 30));
        $this->given(new HoursCredited(Time::now(), 'Bar', 'you@foo', 'w@foo', 'Two', 45));

        $this->given(new CreditorAuthorized(Time::now(), 'Foo', 'c@foo'));

        $this->given(new TokenGenerated(Time::now(), 'my token', 'c@foo'));

        $this->when(new CheckCreditedHours('my token'));
        $this->then->returnShouldMatch(function (CreditedHours $p) {
            return $p->getHistory() == [
                new HoursCredited(Time::now(), 'Foo', 'me@foo', 'v@foo', 'One', 30)
            ];
        });
    }

    function successAsAdministrator() {
        $this->given(new HoursCredited(Time::now(), 'Foo', 'me@foo', 'v@foo', 'One', 30));
        $this->given(new HoursCredited(Time::now(), 'Bar', 'you@foo', 'w@foo', 'Two', 45));

        $this->given(new OrganisationRegistered(Time::now(), 'Foo', 'admin@foo', new Binary('foo'), new Binary('foo key')));

        $this->given(new TokenGenerated(Time::now(), 'my token', 'admin@foo'));

        $this->when(new CheckCreditedHours('my token'));
        $this->then->returnShouldMatch(function (CreditedHours $p) {
            return $p->getHistory() == [
                new HoursCredited(Time::now(), 'Foo', 'me@foo', 'v@foo', 'One', 30)
            ];
        });
    }

    function invalidatedToken() {
        $this->given(new HoursCredited(Time::now(), 'Foo', 'me@foo', 'v@foo', 'One', 30));
        $this->given(new CreditorAuthorized(Time::now(), 'Foo', 'c@foo'));
        $this->given(new TokenGenerated(Time::now(), 'my token', 'c@foo'));
        $this->given(new TokenDestroyed(Time::now(), 'my token'));

        $this->when(new CheckCreditedHours('my token'));
        $this->then->returnShouldMatch(function (CreditedHours $p) {
            return $p->getHistory() == [];
        });
    }
}