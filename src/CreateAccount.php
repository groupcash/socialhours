<?php
namespace groupcash\socialhours;

/**
 * Creates a new account authenticated by email.
 */
class CreateAccount {

    /** @var string */
    private $email;
    /** @var null|string */
    private $name;

    /**
     * @param string $email
     * @param null|string $name
     */
    public function __construct($email, $name = null) {
        $this->email = trim(strtolower($email));
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }
}