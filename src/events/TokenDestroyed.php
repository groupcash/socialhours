<?php
namespace groupcash\socialhours\events;

use groupcash\socialhours\model\Token;

class TokenDestroyed {

    /** @var \DateTimeImmutable */
    private $when;
    /** @var Token */
    private $token;

    /**
     * @param \DateTimeImmutable $when
     * @param Token $token
     */
    public function __construct(\DateTimeImmutable $when, Token $token) {
        $this->when = $when;
        $this->token = $token;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getWhen() {
        return $this->when;
    }

    /**
     * @return Token
     */
    public function getToken() {
        return $this->token;
    }
}