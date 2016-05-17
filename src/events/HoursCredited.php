<?php
namespace groupcash\socialhours\events;

use groupcash\php\model\signing\Binary;

class HoursCredited {

    /** @var \DateTimeImmutable */
    private $when;
    /** @var Binary */
    private $creditor;
    /** @var Binary */
    private $organisation;
    /** @var Binary */
    private $volunteer;
    /** @var string */
    private $description;
    /** @var int */
    private $minutes;

    /**
     * @param \DateTimeImmutable $when
     * @param Binary $creditor
     * @param Binary $organisation
     * @param Binary $volunteer
     * @param string $description
     * @param int $minutes
     */
    public function __construct(\DateTimeImmutable $when, Binary $creditor, Binary $organisation, Binary $volunteer, $description, $minutes) {
        $this->when = $when;
        $this->organisation = $organisation;
        $this->creditor = $creditor;
        $this->volunteer = $volunteer;
        $this->description = $description;
        $this->minutes = $minutes;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getWhen() {
        return $this->when;
    }

    /**
     * @return Binary
     */
    public function getCreditor() {
        return $this->creditor;
    }

    /**
     * @return Binary
     */
    public function getOrganisation() {
        return $this->organisation;
    }

    /**
     * @return Binary
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