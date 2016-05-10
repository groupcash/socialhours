<?php
namespace groupcash\socialhours\model;

use groupcash\php\Groupcash;
use groupcash\socialhours\AuthorizeCreditor;
use groupcash\socialhours\CreateAccount;
use groupcash\socialhours\events\AccountCreated;
use groupcash\socialhours\events\CreditorAuthorized;
use groupcash\socialhours\events\OrganisationRegistered;
use groupcash\socialhours\events\TokenDestroyed;
use groupcash\socialhours\events\TokenGenerated;
use groupcash\socialhours\LogIn;
use groupcash\socialhours\LogOut;
use groupcash\socialhours\RegisterOrganisation;

class SocialHours {

    /** @var Groupcash */
    private $groupcash;
    /** @var string[] Emails */
    private $accounts = [];
    /** @var string[] Names indexed by emails */
    private $organisations = [];
    /** @var string[] Emails indexed by tokens */
    private $activeTokens = [];

    public function __construct(Groupcash $groupcash) {
        $this->groupcash = $groupcash;
    }

    /**
     * @param $binary
     * @return string
     */
    public static function tokenFromBinary($binary) {
        return substr((string)$binary, -16);
    }

    public function handleCreateAccount(CreateAccount $c) {
        $this->guardUniqueEmail($c->getEmail());

        $key = $this->groupcash->generateKey();
        $address = $this->groupcash->getAddress($key);

        return new AccountCreated(
            Time::now(),
            $c->getEmail(),
            $address,
            $key,
            $c->getName()
        );
    }

    public function applyAccountCreated(AccountCreated $e) {
        $this->accounts[] = $e->getEmail();
    }

    public function handleRegisterOrganisation(RegisterOrganisation $c) {
        $this->guardUniqueEmail($c->getAdminEmail());
        $this->guardUniqueName($c->getName());

        $key = $this->groupcash->generateKey();
        $address = $this->groupcash->getAddress($key);

        return new OrganisationRegistered(
            Time::now(),
            $c->getName(),
            $c->getAdminEmail(),
            $address,
            $key
        );
    }

    public function applyOrganisationRegistered(OrganisationRegistered $e) {
        $this->organisations[$e->getAdminEmail()] = $e->getName();
    }

    private function guardUniqueEmail($email) {
        if (isset($this->organisations[$email])) {
            throw new \Exception('An organisation with this email address is already registered.');
        }
        if (in_array($email, $this->accounts)) {
            throw new \Exception('An account with this email address was already created.');
        }
    }

    private function guardUniqueName($name) {
        if (in_array(strtolower($name), array_map('strtolower', $this->organisations))) {
            throw new \Exception('An organisation with this name is already registered.');
        }
    }

    public function handleLogIn(LogIn $c) {
        if (!in_array($c->getEmail(), $this->accounts) && !isset($this->organisations[$c->getEmail()])) {
            throw new \Exception('No account with this email address exists.');
        }

        return new TokenGenerated(
            Time::now(),
            self::tokenFromBinary($this->groupcash->generateKey()),
            $c->getEmail()
        );
    }

    public function applyTokenGenerated(TokenGenerated $e) {
        $this->activeTokens[$e->getToken()] = $e->getEmail();
    }

    public function handleLogOut(LogOut $c) {
        $this->guardValidToken($c->getToken());

        return new TokenDestroyed(
            Time::now(),
            $c->getToken()
        );
    }

    public function handleAuthorizeCreditor(AuthorizeCreditor $c) {
        $this->guardValidToken($c->getToken());

        if (!isset($this->organisations[$this->activeTokens[$c->getToken()]])) {
            throw new \Exception('Only administrators of organisations can authorize creditors.');
        }

        if (!in_array($c->getCreditorEmail(), $this->accounts)) {
            throw new \Exception('No account was created with this email address.');
        }

        return new CreditorAuthorized(
            Time::now(),
            $this->organisations[$this->activeTokens[$c->getToken()]],
            $c->getCreditorEmail()
        );
    }

    private function guardValidToken($token) {
        if (!isset($this->activeTokens[$token])) {
            throw new \Exception('Invalid token.');
        }
    }
}