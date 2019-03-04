<?php

namespace Application\Security;

class WebsiteCop
{
    private static $counterCsrfToken;

    /**
     * @return mixed
     */
    public static function getCounterCsrfToken()
    {
        return self::$counterCsrfToken;
    }

    /**
     * @param mixed $counterCsrfToken
     */
    public static function setCounterCsrfToken($counterCsrfToken): void
    {
        self::$counterCsrfToken = $counterCsrfToken;
    }

    /**
     * Check if the token stored in $_SESSION is the good one
     *
     * @return bool
     */
    public static function isCsrfSafe(): bool
    {
        if (isset($_SESSION['csrf-token']) && $_SESSION['csrf-token'] === self::$counterCsrfToken) {
            return true;
        }
        return false;
    }
}