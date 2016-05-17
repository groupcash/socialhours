<?php
namespace groupcash\socialhours\events;

use groupcash\php\model\signing\Binary;
use groupcash\socialhours\model\Token;

class TokenGenerated {

    /** @var \DateTimeImmutable */
    private $when;
    /** @var Token */
    private $token;
    /** @var Binary */
    private $address;
    /** @var string */
    private $email;

    /**
     * @param \DateTimeImmutable $when
     * @param Token $token
     * @param Binary $address
     * @param string $email
     */
    public function __construct(\DateTimeImmutable $when, Token $token, Binary $address, $email) {
        $this->when = $when;
        $this->token = $token;
        $this->address = $address;
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
     * @return Binary
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }
}