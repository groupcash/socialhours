<?php
namespace groupcash\socialhours\model;

interface PostOffice {

    /**
     * @param string $receiver
     * @param string $subject
     * @param string $message
     * @return null
     */
    public function send($receiver, $subject, $message);
}