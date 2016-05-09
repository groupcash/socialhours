<?php
namespace groupcash\socialhours;

class CreateAccount {

    /** @var string */
    private $email;

    /**
     * @param string $email
     */
    public function __construct($email) {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }
}