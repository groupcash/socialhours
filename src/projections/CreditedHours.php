<?php
namespace groupcash\socialhours\projections;

use groupcash\socialhours\CheckCreditedHours;
use groupcash\socialhours\events\CreditorAuthorized;
use groupcash\socialhours\events\HoursCredited;
use groupcash\socialhours\events\OrganisationRegistered;
use groupcash\socialhours\events\TokenDestroyed;
use groupcash\socialhours\events\TokenGenerated;

class CreditedHours {

    /** @var string */
    private $token;
    /** @var string Email indexed by token */
    private $activeTokens;
    /** @var HoursCredited[] */
    private $history = [];
    /** @var string[][] Organisations indexed by email */
    private $creditors = [];
    /** @var string[] Name indexed by email of administrator */
    private $organisations = [];

    /**
     * @param CheckCreditedHours $query
     */
    public function __construct(CheckCreditedHours $query) {
        $this->token = $query->getToken();
    }

    public function applyTokenGenerated(TokenGenerated $e) {
        $this->activeTokens[$e->getToken()] = $e->getEmail();
    }

    public function applyTokenDestroyed(TokenDestroyed $e) {
        unset($this->activeTokens[$e->getToken()]);
    }

    public function applyCreditorAuthorized(CreditorAuthorized $e) {
        $this->creditors[$e->getCreditorEmail()][] = $e->getOrganisation();
    }

    public function applyOrganisationRegistered(OrganisationRegistered $e) {
        $this->organisations[$e->getAdminEmail()] = $e->getName();
    }

    public function applyHoursCredited(HoursCredited $e) {
        $this->history[] = $e;
    }

    public function getTotalHours() {
        return array_sum(array_map(function (HoursCredited $e) {
            return $e->getMinutes();
        }, $this->filteredHistory())) / 60;
    }

    public function getHistory() {
        return $this->filteredHistory();
    }

    private function filteredHistory() {
        return array_filter($this->history, function (HoursCredited $e) {
            return
                isset($this->activeTokens[$this->token])
                && (
                    isset($this->creditors[$this->activeTokens[$this->token]])
                    && in_array($e->getOrganisation(), $this->creditors[$this->activeTokens[$this->token]])
                    ||
                    isset($this->organisations[$this->activeTokens[$this->token]])
                    && $this->organisations[$this->activeTokens[$this->token]] == $e->getOrganisation()
                );
        });
    }
}