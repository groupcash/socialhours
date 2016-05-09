<?php
namespace groupcash\socialhours\events;

class TokenDestroyed {

    /** @var \DateTimeImmutable */
    private $when;
    /** @var string */
    private $token;

    /**
     * @param \DateTimeImmutable $when
     * @param string $token
     */
    public function __construct(\DateTimeImmutable $when, $token) {
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
     * @return string
     */
    public function getToken() {
        return $this->token;
    }
}