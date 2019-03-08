<?php

namespace Application\Security;

use Application\Exception\CsrfSecurityException;

class CsrfProtector
{
    private static $counterCsrfToken;

    private function __construct()
    {}

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
     * Check if there is a CSRF attack
     *
     * @throws CsrfSecurityException
     */
    public static function checkCsrf()
    {
        if (!self::isCsrfSafe()) {
            throw new CsrfSecurityException('CSRF attack reported!');
        }
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