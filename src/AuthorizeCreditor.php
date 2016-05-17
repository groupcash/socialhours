<?php
namespace groupcash\socialhours;

use groupcash\socialhours\model\AccountIdentifier;
use groupcash\socialhours\model\Token;

/**
 * Authorizes an account to be able to credit social hours in the name of an organisation.
 */
class AuthorizeCreditor {

    /** @var Token */
    private $token;
    /** @var AccountIdentifier */
    private $creditor;

    /**
     * @param Token $token
     * @param AccountIdentifier $creditor
     */
    public function __construct(Token $token, AccountIdentifier $creditor) {
        $this->token = $token;
        $this->creditor = $creditor;
    }

    /**
     * @return Token
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @return AccountIdentifier
     */
    public function getCreditor() {
        return $this->creditor;
    }
}