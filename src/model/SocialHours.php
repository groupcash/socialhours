<?php
namespace groupcash\socialhours\model;

use groupcash\php\Groupcash;
use groupcash\socialhours\CreateAccount;
use groupcash\socialhours\events\AccountCreated;

class SocialHours {

    /** @var Groupcash */
    private $groupcash;

    public function __construct(Groupcash $groupcash) {
        $this->groupcash = $groupcash;
    }

    public function handleCreateAccount(CreateAccount $c) {
        $key = $this->groupcash->generateKey();
        $address = $this->groupcash->getAddress($key);

        return new AccountCreated(
            Time::now(),
            $c->getEmail(),
            $address,
            $key
        );
    }
}