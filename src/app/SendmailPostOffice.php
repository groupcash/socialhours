<?php
namespace groupcash\socialhours\app;

use groupcash\socialhours\model\PostOffice;

class SendmailPostOffice implements PostOffice {

    /** @var string */
    private $sender;

    /**
     * @param string $sender
     */
    public function __construct($sender) {
        $this->sender = $sender;
    }

    /**
     * @param string $receiver
     * @param string $subject
     * @param string $message
     * @return null
     */
    public function send($receiver, $subject, $message) {
        mail($receiver, $subject, $message, "From: {$this->sender}");
    }
}