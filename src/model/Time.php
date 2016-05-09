<?php
namespace groupcash\socialhours\model;

class Time {

    private static $frozen;

    public static function freeze(\DateTimeImmutable $when) {
        self::$frozen = $when;
    }

    /**
     * @return \DateTimeImmutable
     */
    public static function now() {
        return self::$frozen ?: new \DateTimeImmutable();
    }
}