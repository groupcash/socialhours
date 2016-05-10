<?php
namespace spec\groupcash\socialhours;

use groupcash\socialhours\AuthorizeCreditor;
use groupcash\socialhours\events\TokenDestroyed;
use groupcash\socialhours\events\TokenGenerated;
use groupcash\socialhours\LogOut;
use groupcash\socialhours\model\Time;

class LogOutSpec extends SocialHoursSpecification {

    function invalidToken() {
        $this->when->tryTo(new LogOut('some token'));
        $this->then->shouldFail('Invalid token.');
    }

    function success() {
        $this->given(new TokenGenerated(Time::now(), 'some token', 'foo@bar.com'));
        $this->when(new LogOut('some token'));
        $this->then(new TokenDestroyed(Time::now(), 'some token'));
    }

    function invalidateToken() {
        $this->given(new TokenGenerated(Time::now(), 'some token', 'foo@bar'));
        $this->given(new TokenDestroyed(Time::now(), 'some token'));
        $this->when->tryTo(new AuthorizeCreditor('some token', 'creditor@foo'));
        $this->then->shouldFail('Invalid token.');
    }
}