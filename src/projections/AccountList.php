<?php
namespace groupcash\socialhours\projections;

use groupcash\php\model\signing\Binary;
use groupcash\socialhours\events\AccountCreated;

class AccountList {

    /** @var AccountCreated[] indexed by address */
    private $accounts = [];

    /**
     * @return Binary[]
     */
    public function getAddresses() {
        return array_map(function (AccountCreated $e) {
            return $e->getAddress();
        }, $this->accounts);
    }

    /**
     * @param Binary $address
     * @return string
     */
    public function getEmail(Binary $address) {
        return $this->accounts[(string)$address]->getEmail();
    }

    public function applyAccountCreated(AccountCreated $e) {
        $this->accounts[(string)$e->getAddress()] = $e;
    }
}