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

        $this->given(new AccountCreated(Time::now(), 'foo@bar.com', new Binary('my key'), new Binary('my address')));
    }

    function emailNotExisting() {
        $this->when->tryTo(new LogIn('not@bar.com'));
        $this->then->shouldFail('No account with this email address exists.');
    }

    function generateTokenForAccount() {
        $this->when(new LogIn('foo@bar.com'));
        $this->then(new TokenGenerated(
            Time::now(),
            $this->token,
            'foo@bar.com'
        ));
    }

    function generateTokenForOrganisation() {
        $this->given(new OrganisationRegistered(Time::now(), 'Foo', 'baz@bar.com', new Binary('my key'), new Binary('my address')));
        $this->when(new LogIn('baz@bar.com'));
        $this->then(new TokenGenerated(
            Time::now(),
            $this->token,
            'baz@bar.com'
        ));
    }

    function ignoreEmailCaseAndSpaces() {
        $this->when(new LogIn(' FOO@bar.com '));
        $this->then(new TokenGenerated(
            Time::now(),
            $this->token,
            'foo@bar.com'
        ));
    }

    function sendEmail() {
        $this->when(new LogIn('foo@bar.com'));
        Mockster::stub($this->postOffice->send(
            'foo@bar.com', Argument::string(), Argument::contains((string)$this->token)
        ))->shouldHave()->beenCalled(1);
    }
}