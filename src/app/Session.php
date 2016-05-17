<?php
namespace groupcash\socialhours\app;

use groupcash\socialhours\model\Token;

class Session {

    public function __construct() {
        session_start();
    }

    public function start(Token $token) {
        $_SESSION['token'] = (string)$token;
    }

    public function stop() {
        unset($_SESSION['token']);
    }

    public function getToken() {
        return new Token($_SESSION['token']);
    }

    public function isStarted() {
        return isset($_SESSION['token']) && $_SESSION['token'];
    }
}