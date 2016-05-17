<?php
namespace groupcash\socialhours;

/**
 * Lists all social hours credited by an organisation.
 */
class CheckCreditedHours {

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