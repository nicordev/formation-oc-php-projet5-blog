<?php

namespace Application\Security;

use Application\Exception\AppException;
use Application\Exception\BlogException;
use Model\Entity\ConnectionTry;
use Model\Manager\ConnectionTryManager;

class BruteForceProtector
{
    private const MYSQL_DATE_FORMAT = "Y-m-d H:i:s";
    private const NUMBER_OF_TRIES = 3;
    private const DELAY_BEFORE_RESET = 20; // In seconds

    private function __construct()
    {}

    /**
     * Check the number of tries and the delay
     *
     * @return int
     * @throws AppException
     */
    public static function canConnectAgainIn(): int
    {
        $connectionTryManager = new ConnectionTryManager();
        $userKey = self::getUserKey();

        try {
            $connectionTry = $connectionTryManager->get(null, $userKey);

            if ($connectionTry->getCount() >= self::NUMBER_OF_TRIES) {
                $waitingTime = self::calculateWaitingTime($connectionTry->getLastTry());
                if ($waitingTime === 0) {
                    $connectionTryManager->delete($connectionTry->getId());
                    return 0;
                }
                return $waitingTime;
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
        return 0;
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
     * Calculate the remaining waiting time
     *
     * @param string $lastTry
     * @return false|int
     */
    private static function calculateWaitingTime(string $lastTry)
    {
        $waitingTime = self::DELAY_BEFORE_RESET - self::calculateTimeSpent($lastTry);

        if ($waitingTime > 0) {
            return $waitingTime;
        }
        return 0;
    }

    /**
     * Calculate time spent from the last try
     *
     * @param string $lastTry
     * @return false|int
     */
    private static function calculateTimeSpent(string $lastTry)
    {
        $now = time();
        $lastTry = strtotime($lastTry);
        return $now - $lastTry;
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