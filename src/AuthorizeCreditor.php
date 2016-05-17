<?php
namespace groupcash\socialhours;
use groupcash\socialhours\model\Token;

/**
 * Authorizes an account to be able to credit social hours in the name of an organisation.
 */
class AuthorizeCreditor {

    /** @var Token */
    private $token;
    /** @var string */
    private $creditorEmail;

    /**
     * @param Token $token
     * @param string $creditorEmail
     */
    public function __construct(Token $token, $creditorEmail) {
        $this->token = $token;
        $this->creditorEmail = $creditorEmail;
    }

    /**
     * @return Token
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