<?php
namespace groupcash\socialhours\events;

use groupcash\php\model\signing\Binary;

class CreditorAuthorized {

    /** @var \DateTimeImmutable */
    private $when;
    /** @var Binary */
    private $organisation;
    /** @var Binary */
    private $creditor;

    /**
     * @param \DateTimeImmutable $when
     * @param Binary $organisation
     * @param Binary $creditor
     */
    public function __construct(\DateTimeImmutable $when, Binary $organisation, Binary $creditor) {
        $this->when = $when;
        $this->organisation = $organisation;
        $this->creditor = $creditor;
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
    public function getOrganisation() {
        return $this->organisation;
    }

    /**
     * @return Binary
     */
    public function getCreditor() {
        return $this->creditor;
    }
}