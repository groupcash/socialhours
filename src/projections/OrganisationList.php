<?php
namespace groupcash\socialhours\projections;

use groupcash\php\model\signing\Binary;
use groupcash\socialhours\events\OrganisationRegistered;

class OrganisationList {

    /** @var OrganisationRegistered[] */
    private $organisations = [];

    /**
     * @return Binary[]
     */
    public function getAddresses() {
        return array_map(function (OrganisationRegistered $e) {
            return $e->getAddress();
        }, $this->organisations);
    }

    /**
     * @param Binary $address
     * @return string
     */
    public function getEmail(Binary $address) {
        return $this->organisations[(string)$address]->getAdminEmail();
    }

    /**
     * @param Binary $address
     * @return string
     */
    public function getName(Binary $address) {
        return $this->organisations[(string)$address]->getName();
    }

    public function applyOrganisationRegistered(OrganisationRegistered $e) {
        $this->organisations[(string)$e->getAddress()] = $e;
    }
}