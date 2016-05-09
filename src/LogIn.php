<?php
namespace groupcash\socialhours;

class LogIn {
    /** @var string */
    private $email;

    /**
     * @param string $email
     */
    public function __construct($email) {
        $this->email = trim(strtolower($email));
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }
}