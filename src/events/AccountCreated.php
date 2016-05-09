<?php
namespace groupcash\socialhours\events;

use groupcash\php\model\signing\Binary;

class AccountCreated {

    /** @var \DateTimeImmutable */
    private $when;
    /** @var string */
    private $email;
    /** @var Binary */
    private $address;
    /** @var Binary */
    private $key;

    /**
     * @param \DateTimeImmutable $when
     * @param string $email
     * @param Binary $address
     * @param Binary $key
     */
    public function __construct(\DateTimeImmutable $when, $email, Binary $address, Binary $key) {
        $this->when = $when;
        $this->email = $email;
        $this->address = $address;
        $this->key = $key;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getWhen() {
        return $this->when;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @return Binary
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @return Binary
     */
    public function getKey() {
        return $this->key;
    }
}