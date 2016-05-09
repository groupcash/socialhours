<?php
namespace groupcash\socialhours;

class RegisterOrganisation {

    /** @var string */
    private $name;
    /** @var string */
    private $adminEmail;

    /**
     * @param string $name
     * @param string $adminEmail
     */
    public function __construct($name, $adminEmail) {
        $this->name = trim($name);
        $this->adminEmail = trim(strtolower($adminEmail));
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
}