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
    /** @var null|string */
    private $name;

    /**
     * @param \DateTimeImmutable $when
     * @param Binary $address
     * @param Binary $key
     * @param string $email
     * @param null|string $name
     */
    public function __construct(\DateTimeImmutable $when, Binary $address, Binary $key, $email, $name = null) {
        $this->when = $when;
        $this->email = $email;
        $this->address = $address;
        $this->key = $key;
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getName() {
        return $this->name;
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