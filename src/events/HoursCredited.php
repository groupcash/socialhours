<?php
namespace groupcash\socialhours\events;

class HoursCredited {

    /** @var string */
    private $organisation;
    /** @var string */
    private $volunteerEmail;
    /** @var string */
    private $description;
    /** @var int */
    private $minutes;
    /** @var \DateTimeImmutable */
    private $when;

    /**
     * @param \DateTimeImmutable $when
     * @param string $organisation
     * @param string $volunteerEmail
     * @param string $description
     * @param int $minutes
     */
    public function __construct(\DateTimeImmutable $when, $organisation, $volunteerEmail, $description, $minutes) {
        $this->organisation = $organisation;
        $this->volunteerEmail = $volunteerEmail;
        $this->description = $description;
        $this->minutes = $minutes;
        $this->when = $when;
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
    public function getOrganisation() {
        return $this->organisation;
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