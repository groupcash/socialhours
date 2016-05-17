<?php
namespace spec\groupcash\socialhours;

use groupcash\php\model\signing\Binary;
use groupcash\socialhours\events\AccountCreated;
use groupcash\socialhours\events\OrganisationRegistered;
use groupcash\socialhours\events\TokenGenerated;
use groupcash\socialhours\LogIn;
use groupcash\socialhours\model\SocialHours;
use groupcash\socialhours\model\Time;
use groupcash\socialhours\model\Token;
use rtens\mockster\arguments\Argument;
use rtens\mockster\Mockster;

/**
 * @property Token token
 */
class LogInSpec extends SocialHoursSpecification {

    function before() {
        $this->algorithm->nextKey = 'this is the brand new token';
        $this->token = SocialHours::tokenFromBinary(new Binary('this is the brand new token key'));

        $this->given(new AccountCreated(Time::now(), new Binary('foo'), new Binary('foo key'), 'foo'));
    }

    function emailNotExisting() {
        $this->when->tryTo(new LogIn('not@bar.com'));
        $this->then->shouldFail('No account with this email address exists.');
    }

    function generateTokenForAccount() {
        $this->when(new LogIn('foo'));
        $this->then(new TokenGenerated(
            Time::now(), $this->token, new Binary('foo'), 'foo'
        ));
    }

    function generateTokenForOrganisation() {
        $this->given(new OrganisationRegistered(Time::now(), new Binary('bar'), new Binary('bar key'), 'bar', 'Bar'));
        $this->when(new LogIn('bar'));
        $this->then(new TokenGenerated(
            Time::now(), $this->token, new Binary('bar'), 'bar'
        ));
    }

    function ignoreEmailCaseAndSpaces() {
        $this->when(new LogIn(' FOO '));
        $this->then(new TokenGenerated(
            Time::now(), $this->token, new Binary('foo'), 'foo'
        ));
    }

    function sendEmail() {
        $this->when(new LogIn('foo'));
        Mockster::stub($this->postOffice->send(
            'foo', Argument::string(), Argument::contains((string)$this->token)
        ))->shouldHave()->beenCalled(1);
    }
}