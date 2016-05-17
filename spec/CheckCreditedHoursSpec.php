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
use groupcash\socialhours\model\Token;
use groupcash\socialhours\projections\CreditedHours;
use rtens\scrut\failures\IncompleteTestFailure;

class CheckCreditedHoursSpec extends SocialHoursSpecification {

    function before() {
        throw new IncompleteTestFailure();
    }

    function invalidToken() {
        $this->when(new CheckCreditedHours(new Token('wrong token')));
        $this->then->returnShouldMatch(function (CreditedHours $p) {
            return $p->getHistory() == [] && $p->getTotalHours() == 0;
        });
    }

    function successAsCreditor() {
        $this->given(new HoursCredited(Time::now(), new Binary('baz'), new Binary('foo'), new Binary('bar'), 'One', 30));
        $this->given(new CreditorAuthorized(Time::now(), new Binary('foo'), new Binary('baz')));
        $this->given(new TokenGenerated(Time::now(), new Token('my token'), new Binary('baz'), 'email'));

        $this->when(new CheckCreditedHours(new Token('my token')));
        $this->then->returnShouldMatch(function (CreditedHours $p) {
            return $p->getHistory() == [
                new HoursCredited(Time::now(), new Binary('baz'), new Binary('foo'), new Binary('bar'), 'One', 30)
            ];
        });
    }

    function sumHours() {
        $this->given(new HoursCredited(Time::now(), 'Foo', 'me@foo', 'v@foo', 'One', 30));
        $this->given(new HoursCredited(Time::now(), 'Foo', 'me@foo', 'v@foo', 'Two', 45));
        $this->given(new CreditorAuthorized(Time::now(), 'Foo', 'c@foo'));
        $this->given(new TokenGenerated(Time::now(), new Token('my token'), 'c@foo', 'email'));

        $this->when(new CheckCreditedHours(new Token('my token')));
        $this->then->returnShouldMatch(function (CreditedHours $p) {
            return $p->getTotalHours() == 1.25;
        });
    }

    function notCreditor() {
        $this->given(new HoursCredited(Time::now(), 'Foo', 'me@foo', 'v@foo', 'One', 30));
        $this->given(new TokenGenerated(Time::now(), new Token('my token'), 'c@foo', 'email'));

        $this->when(new CheckCreditedHours(new Token('my token')));
        $this->then->returnShouldMatch(function (CreditedHours $p) {
            return $p->getHistory() == [];
        });
    }

    function creditorOfManyOrganisations() {
        $this->given(new HoursCredited(Time::now(), 'Foo', 'me@foo', 'v@foo', 'One', 30));
        $this->given(new HoursCredited(Time::now(), 'Bar', 'you@foo', 'w@foo', 'Two', 45));

        $this->given(new CreditorAuthorized(Time::now(), 'Foo', 'c@foo'));
        $this->given(new CreditorAuthorized(Time::now(), 'Bar', 'c@foo'));

        $this->given(new TokenGenerated(Time::now(), new Token('my token'), 'c@foo', 'email'));

        $this->when(new CheckCreditedHours(new Token('my token')));
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

        $this->given(new TokenGenerated(Time::now(), new Token('my token'), 'c@foo', 'email'));

        $this->when(new CheckCreditedHours(new Token('my token')));
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

        $this->given(new TokenGenerated(Time::now(), new Token('my token'), 'admin@foo', 'email'));

        $this->when(new CheckCreditedHours(new Token('my token')));
        $this->then->returnShouldMatch(function (CreditedHours $p) {
            return $p->getHistory() == [
                new HoursCredited(Time::now(), 'Foo', 'me@foo', 'v@foo', 'One', 30)
            ];
        });
    }

    function invalidatedToken() {
        $this->given(new HoursCredited(Time::now(), 'Foo', 'me@foo', 'v@foo', 'One', 30));
        $this->given(new CreditorAuthorized(Time::now(), 'Foo', 'c@foo'));
        $this->given(new TokenGenerated(Time::now(), new Token('my token'), 'c@foo', 'email'));
        $this->given(new TokenDestroyed(Time::now(), new Token('my token')));

        $this->when(new CheckCreditedHours(new Token('my token')));
        $this->then->returnShouldMatch(function (CreditedHours $p) {
            return $p->getHistory() == [];
        });
    }
}