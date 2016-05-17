<?php
namespace spec\groupcash\socialhours;

use groupcash\socialhours\AuthorizeCreditor;
use groupcash\socialhours\events\TokenDestroyed;
use groupcash\socialhours\events\TokenGenerated;
use groupcash\socialhours\LogOut;
use groupcash\socialhours\model\Time;
use groupcash\socialhours\model\Token;

class LogOutSpec extends SocialHoursSpecification {

    function invalidToken() {
        $this->when->tryTo(new LogOut(new Token('some token')));
        $this->then->shouldFail('Invalid token.');
    }

    function success() {
        $this->given(new TokenGenerated(Time::now(), new Token('some token'), 'foo@bar.com'));
        $this->when(new LogOut(new Token('some token')));
        $this->then(new TokenDestroyed(Time::now(), new Token('some token')));
    }

    function invalidateToken() {
        $this->given(new TokenGenerated(Time::now(), new Token('some token'), 'foo@bar'));
        $this->given(new TokenDestroyed(Time::now(), new Token('some token')));
        $this->when->tryTo(new AuthorizeCreditor(new Token('some token'), 'creditor@foo'));
        $this->then->shouldFail('Invalid token.');
    }
}