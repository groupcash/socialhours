<?php
namespace groupcash\socialhours;

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