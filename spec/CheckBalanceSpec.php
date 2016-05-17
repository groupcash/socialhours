<?php
namespace spec\groupcash\socialhours;

use groupcash\socialhours\CheckBalance;
use groupcash\socialhours\events\HoursCredited;
use groupcash\socialhours\events\TokenDestroyed;
use groupcash\socialhours\events\TokenGenerated;
use groupcash\socialhours\model\Time;
use groupcash\socialhours\model\Token;
use groupcash\socialhours\projections\Balance;

class CheckBalanceSpec extends SocialHoursSpecification {

    function noBalance() {
        $this->given(new TokenGenerated(Time::now(), new Token('some token'), 'foo@bar'));
        $this->when(new CheckBalance(new Token('some token')));
        $this->then->returnShouldMatchAll(function (Balance $balance) {
            return [
                [$balance->getHistory(), []],
                [$balance->getTotalHours(), 0]
            ];
        });
    }

    function oneCredit() {
        $this->given(new HoursCredited(Time::now(), 'Foo', 'me@foo', 'foo@bar', 'One', 30));
        $this->given(new TokenGenerated(Time::now(), new Token('some token'), 'foo@bar'));
        $this->when(new CheckBalance(new Token('some token')));
        $this->then->returnShouldMatch(function (Balance $balance) {
            return $balance->getHistory() == [
                new HoursCredited(Time::now(), 'Foo', 'me@foo', 'foo@bar', 'One', 30)
            ];
        });
    }

    function sumHours() {
        $this->given(new HoursCredited(Time::now(), 'Foo', 'me@foo', 'foo@bar', 'One', 30));
        $this->given(new HoursCredited(Time::now(), 'Foo', 'me@foo', 'foo@bar', 'Two', 45));
        $this->given(new TokenGenerated(Time::now(), new Token('some token'), 'foo@bar'));
        $this->when(new CheckBalance(new Token('some token')));
        $this->then->returnShouldMatch(function (Balance $balance) {
            return $balance->getTotalHours() == 1.25;
        });
    }

    function invalidToken() {
        $this->given(new HoursCredited(Time::now(), 'Foo', 'me@foo', 'foo@bar', 'One', 30));
        $this->given(new TokenGenerated(Time::now(), new Token('some token'), 'foo@bar'));
        $this->when(new CheckBalance(new Token('wrong token')));
        $this->then->returnShouldMatchAll(function (Balance $balance) {
            return [
                [$balance->getHistory(), []],
                [$balance->getTotalHours(), 0]
            ];
        });
    }

    function invalidatedToken() {
        $this->given(new HoursCredited(Time::now(), 'Foo', 'me@foo', 'foo@bar', 'One', 30));
        $this->given(new TokenGenerated(Time::now(), new Token('some token'), 'foo@bar'));
        $this->given(new TokenDestroyed(Time::now(), new Token('some token')));
        $this->when(new CheckBalance(new Token('some token')));
        $this->then->returnShouldMatch(function (Balance $balance) {
            return $balance->getHistory() == [];
        });
    }
}