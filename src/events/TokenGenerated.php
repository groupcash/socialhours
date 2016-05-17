<?php
namespace groupcash\socialhours\events;

use groupcash\socialhours\model\Token;

class TokenGenerated {

    /** @var \DateTimeImmutable */
    private $when;
    /** @var Token */
    private $token;
    /** @var string */
    private $email;

    /**
     * @param \DateTimeImmutable $when
     * @param Token $token
     * @param string $email
     */
    public function __construct(\DateTimeImmutable $when, Token $token, $email) {
        $this->when = $when;
        $this->token = $token;
        $this->email = $email;
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

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }
}