<?php
namespace groupcash\socialhours;
use groupcash\socialhours\model\Token;

/**
 * Lists the received social hours of an account.
 */
class CheckBalance {

    /** @var Token */
    private $token;

    /**
     * @param Token $token
     */
    public function __construct(Token $token) {
        $this->token = $token;
    }

    /**
     * @return Token
     */
    public function getToken() {
        return $this->token;
    }
}