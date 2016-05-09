<?php
namespace groupcash\socialhours\events;

class TokenGenerated {

    /** @var \DateTimeImmutable */
    private $when;
    /** @var string */
    private $token;
    /** @var string */
    private $email;

    /**
     * @param \DateTimeImmutable $when
     * @param string $token
     * @param string $email
     */
    public function __construct(\DateTimeImmutable $when, $token, $email) {
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
     * @return string
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