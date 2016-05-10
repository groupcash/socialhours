<?php
namespace groupcash\socialhours;

class CreditHours {

    /** @var string */
    private $token;
    /** @var string */
    private $volunteerEmail;
    /** @var string */
    private $description;
    /** @var int */
    private $minutes;
    /** @var string */
    private $organisation;

    /**
     * @param string $token
     * @param string $organisation
     * @param string $volunteerEmail
     * @param string $description
     * @param int $minutes
     */
    public function __construct($token, $organisation, $volunteerEmail, $description, $minutes) {
        $this->token = $token;
        $this->volunteerEmail = $volunteerEmail;
        $this->description = $description;
        $this->minutes = $minutes;
        $this->organisation = $organisation;
    }

    /**
     * @return string
     */
    public function getOrganisation() {
        return $this->organisation;
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
    public function getVolunteerEmail() {
        return $this->volunteerEmail;
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