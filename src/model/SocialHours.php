<?php
namespace groupcash\socialhours\model;

use groupcash\php\Groupcash;
use groupcash\socialhours\CreateAccount;
use groupcash\socialhours\events\AccountCreated;

class SocialHours {

    /** @var Groupcash */
    private $groupcash;
    /** @var string[] */
    private $accounts = [];

    public function __construct(Groupcash $groupcash) {
        $this->groupcash = $groupcash;
    }

    public function handleCreateAccount(CreateAccount $c) {
        if (in_array($c->getEmail(), $this->accounts)) {
            throw new \Exception('An account with this email address was already created.');
        }

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
}