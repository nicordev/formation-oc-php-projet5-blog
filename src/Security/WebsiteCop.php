<?php

namespace Application\Security;

use Application\Exception\AppException;
use Application\Exception\BlogException;
use Application\Exception\SecurityException;
use Model\Entity\ConnectionTry;
use Model\Manager\ConnectionTryManager;

class WebsiteCop
{
    private static $counterCsrfToken;
    private $connectionTryManager;

    private const MYSQL_DATE_FORMAT = "Y-m-d H:i:s";
    private const NUMBER_OF_TRIES = 3;
    private const DELAY_BEFORE_RESET = 20; // In seconds

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
     * @throws SecurityException
     */
    public static function checkCsrf()
    {
        if (!self::isCsrfSafe()) {
            throw new SecurityException('CSRF attack reported!');
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

    /**
     * Check the number of tries and the delay
     *
     * @return bool
     * @throws AppException
     */
    public static function canConnectAgain(): bool
    {
        $connectionTryManager = new ConnectionTryManager();
        $userKey = self::getUserKey();

        try {
            $connectionTry = $connectionTryManager->get(null, $userKey);

            if ($connectionTry->getCount() >= self::NUMBER_OF_TRIES) {
                if (self::isAfterDelay($connectionTry->getLastTry())) {
                    $connectionTryManager->delete($connectionTry->getId());
                    return true;
                }
                return false;
            }
            $connectionTry->setCount($connectionTry->getCount() + 1);
            $connectionTry->setLastTry(date(self::MYSQL_DATE_FORMAT));
            $connectionTryManager->edit($connectionTry);

        } catch (BlogException $e) {
            $connectionTry = new ConnectionTry([
                'user' => $userKey,
                'lastTry' => date(self::MYSQL_DATE_FORMAT),
                'count' => 1
            ]);
            $connectionTryManager->add($connectionTry);
        }
        return true;
    }

    /**
     * Delete the user from the bl_connection_try table
     *
     * @throws AppException
     */
    public static function resetTheUser()
    {
        $connectionTryManager = new ConnectionTryManager();
        $userKey = self::getUserKey();
        try {
            $connectionTry = $connectionTryManager->get(null, $userKey);
            $connectionTryManager->delete($connectionTry->getId());
        } catch (BlogException $e) {
            // Nothing to do, the user is not registered
        }
    }

    // Private

    /**
     * Check if the last try is after the delay before reset the tries counter
     *
     * @param string $lastTry
     * @return bool
     */
    private static function isAfterDelay(string $lastTry)
    {
        $now = time();
        $lastTry = strtotime($lastTry);

        if ($now - $lastTry >= self::DELAY_BEFORE_RESET) {
            return true;
        }
        return false;
    }

    /**
     * Get the user identification key from $_SERVER
     *
     * @return string
     */
    private static function getUserKey()
    {
        return "{$_SERVER['SERVER_NAME']}~login:{$_SERVER['REMOTE_ADDR']}";
    }
}