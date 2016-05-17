<?php
namespace groupcash\socialhours;

/**
 * Invalidates a log-in token.
 */
class LogOut {

    /** @var string */
    private $token;

    /**
     * @param string $token
     */
    public function __construct($token) {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken() {
        return $this->token;
    }
}