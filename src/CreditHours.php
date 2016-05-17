<?php
namespace groupcash\socialhours;

use groupcash\socialhours\model\AccountIdentifier;
use groupcash\socialhours\model\OrganisationIdentifier;
use groupcash\socialhours\model\Token;

/**
 * Credits an amount of social hours to an account.
 */
class CreditHours {

    /** @var Token */
    private $token;
    /** @var OrganisationIdentifier */
    private $organisation;
    /** @var AccountIdentifier */
    private $volunteer;
    /** @var string */
    private $description;
    /** @var int */
    private $minutes;

    /**
     * @param Token $token
     * @param OrganisationIdentifier $organisation
     * @param AccountIdentifier $volunteer
     * @param string $description
     * @param int $minutes
     */
    public function __construct(Token $token, OrganisationIdentifier $organisation, AccountIdentifier $volunteer, $description, $minutes) {
        $this->token = $token;
        $this->volunteer = $volunteer;
        $this->description = $description;
        $this->minutes = $minutes;
        $this->organisation = $organisation;
    }

    /**
     * @return Token
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @return OrganisationIdentifier
     */
    public function getOrganisation() {
        return $this->organisation;
    }

    /**
     * @return AccountIdentifier
     */
    public function getVolunteer() {
        return $this->volunteer;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getMinutes() {
        return $this->minutes;
    }
}