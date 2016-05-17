<?php
namespace groupcash\socialhours\model;

class Token {

    /** @var string */
    private $value;

    /**
     * @param string $value
     */
    public function __construct($value) {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    function __toString() {
        return $this->value;
    }
}