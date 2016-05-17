<?php
namespace groupcash\socialhours;

/**
 * Creates a new log-in token for an account.
 *
 * The token will be sent to the email address.
 */
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