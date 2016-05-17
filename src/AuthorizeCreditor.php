<?php
namespace groupcash\socialhours;

/**
 * Authorizes an account to be able to credit social hours in the name of an organisation.
 */
class AuthorizeCreditor {

    /** @var string */
    private $token;
    /** @var string */
    private $creditorEmail;

    /**
     * @param string $token
     * @param string $creditorEmail
     */
    public function __construct($token, $creditorEmail) {
        $this->token = $token;
        $this->creditorEmail = $creditorEmail;
    }

    /**
     * @return string
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getCreditorEmail() {
        return $this->creditorEmail;
    }
}