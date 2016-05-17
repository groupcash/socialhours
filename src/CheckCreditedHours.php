<?php
namespace groupcash\socialhours;
use groupcash\socialhours\model\Token;

/**
 * Lists all social hours credited by an organisation.
 */
class CheckCreditedHours {

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