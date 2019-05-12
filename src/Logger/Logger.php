<?php

namespace Application\Logger;

class Logger
{
    private function __construct()
    {
        // Disabled
    }

    /**
     * Add a log in /log/blog.log
     *
     * @param string $data
     */
    public static function addLog(string $data)
    {
        $data = self::addDateToData($data);
        file_put_contents(ROOT_PATH . "/log/blog.log", $data . "\n", FILE_APPEND);
    }

    /**
     * Save a message sent via the contact form in a log file
     *
     * @param string $name
     * @param string $email
     * @param string $message
     */
    public static function saveContactMessage(string $name, string $email, string $message)
    {
        $message = "$name($email) sent a message: $message\n";
        $data = self::addDateToData($message);
        file_put_contents(ROOT_PATH . "/log/contact_messages.log", $data, FILE_APPEND);
    }

    /**
     * Add the current date to data (and the remote address)
     *
     * @param $data
     * @return string
     */
    private static function addDateToData($data)
    {
        $data = date("Y-m-d H:i:s") . " client: " . ($_SERVER['REMOTE_ADDR'] ?? "") . " message: " . $data;

        return $data;
    }
}
