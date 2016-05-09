<?php
namespace groupcash\socialhours\events;

use groupcash\php\model\signing\Binary;

class OrganisationRegistered {

    /** @var \DateTimeImmutable */
    private $when;
    /** @var string */
    private $name;
    /** @var string */
    private $adminEmail;
    /** @var Binary */
    private $address;
    /** @var Binary */
    private $key;

    /**
     * @param \DateTimeImmutable $when
     * @param string $name
     * @param string $adminEmail
     * @param Binary $address
     * @param Binary $key
     */
    public function __construct($when, $name, $adminEmail, $address, $key) {
        $this->when = $when;
        $this->name = $name;
        $this->adminEmail = $adminEmail;
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
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAdminEmail() {
        return $this->adminEmail;
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