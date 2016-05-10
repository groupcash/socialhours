<?php
namespace groupcash\socialhours\events;

class CreditorAuthorized {

    /** @var \DateTimeImmutable */
    private $when;
    /** @var string */
    private $organisation;
    /** @var string */
    private $creditorEmail;

    /**
     * @param \DateTimeImmutable $when
     * @param string $organisation
     * @param string $creditorEmail
     */
    public function __construct(\DateTimeImmutable $when, $organisation, $creditorEmail) {
        $this->when = $when;
        $this->organisation = $organisation;
        $this->creditorEmail = $creditorEmail;
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
    public function getCreditorEmail() {
        return $this->creditorEmail;
    }
}