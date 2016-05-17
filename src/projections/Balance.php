<?php
namespace groupcash\socialhours\projections;

use groupcash\socialhours\CheckBalance;
use groupcash\socialhours\events\HoursCredited;
use groupcash\socialhours\events\TokenDestroyed;
use groupcash\socialhours\events\TokenGenerated;
use groupcash\socialhours\model\Token;

class Balance {

    /** @var Token */
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
        $this->activeTokens[(string)$e->getToken()] = $e->getEmail();
    }

    public function applyTokenDestroyed(TokenDestroyed $e) {
        unset($this->activeTokens[(string)$e->getToken()]);
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
                isset($this->activeTokens[(string)$this->token])
                && $e->getVolunteerEmail() == $this->activeTokens[(string)$this->token];
        });
    }
}