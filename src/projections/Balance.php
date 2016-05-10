<?php
namespace groupcash\socialhours\projections;

use groupcash\socialhours\CheckBalance;
use groupcash\socialhours\events\HoursCredited;
use groupcash\socialhours\events\TokenDestroyed;
use groupcash\socialhours\events\TokenGenerated;

class Balance {

    /** @var string */
    private $token;
    /** @var string Email indexed by token */
    private $activeTokens;
    /** @var HoursCredited[] */
    private $history = [];

    /**
     * @param CheckBalance $query
     */
    public function __construct(CheckBalance $query) {
        $this->token = $query->getToken();
    }

    public function applyTokenGenerated(TokenGenerated $e) {
        $this->activeTokens[$e->getToken()] = $e->getEmail();
    }

    public function applyTokenDestroyed(TokenDestroyed $e) {
        unset($this->activeTokens[$e->getToken()]);
    }

    public function applyHoursCredited(HoursCredited $e) {
        $this->history[] = $e;
    }

    public function getHistory() {
        return $this->filteredHistory();
    }

    public function getTotalHours() {
        return array_sum(array_map(function (HoursCredited $e) {
            return $e->getMinutes();
        }, $this->filteredHistory())) / 60;
    }

    private function filteredHistory() {
        return array_filter($this->history, function (HoursCredited $e) {
            return
                isset($this->activeTokens[$this->token])
                && $e->getVolunteerEmail() == $this->activeTokens[$this->token];
        });
    }
}