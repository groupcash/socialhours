<?php
namespace groupcash\socialhours\projections;

use groupcash\php\model\signing\Binary;
use groupcash\socialhours\CheckCreditedHours;
use groupcash\socialhours\events\CreditorAuthorized;
use groupcash\socialhours\events\HoursCredited;
use groupcash\socialhours\events\OrganisationRegistered;
use groupcash\socialhours\events\TokenDestroyed;
use groupcash\socialhours\events\TokenGenerated;
use groupcash\socialhours\model\Token;

class CreditedHours {

    /** @var Token */
    private $token;
    /** @var Binary[] indexed by token */
    private $activeTokens = [];
    /** @var HoursCredited[] */
    private $history = [];
    /** @var Binary[][] Organisation addresses grouped by creditor address */
    private $creditors = [];
    /** @var Binary[] */
    private $organisations = [];

    /**
     * @param CheckCreditedHours $query
     */
    public function __construct(CheckCreditedHours $query) {
        $this->token = $query->getToken();
    }

    public function applyTokenGenerated(TokenGenerated $e) {
        $this->activeTokens[(string)$e->getToken()] = $e->getAddress();
    }

    public function applyTokenDestroyed(TokenDestroyed $e) {
        unset($this->activeTokens[(string)$e->getToken()]);
    }

    public function applyCreditorAuthorized(CreditorAuthorized $e) {
        $this->creditors[(string)$e->getCreditor()][] = $e->getOrganisation();
    }

    public function applyOrganisationRegistered(OrganisationRegistered $e) {
        $this->organisations[] = $e->getAddress();
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
            return $this->isValidToken() && $this->isCreditorOrAdministator($e->getOrganisation());
        });
    }

    private function isValidToken() {
        return isset($this->activeTokens[(string)$this->token]);
    }

    private function isCreditorOrAdministator(Binary $organisation) {
        $address = (string)$this->activeTokens[(string)$this->token];

        $isAdministrator = $organisation == $address;
        $isCreditor = isset($this->creditors[$address]) && in_array($organisation, $this->creditors[$address]);

        return $isAdministrator || $isCreditor;
    }
}