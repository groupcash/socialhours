<?php
namespace groupcash\socialhours;
use groupcash\socialhours\model\Token;

/**
 * Invalidates a log-in token.
 */
class LogOut {

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