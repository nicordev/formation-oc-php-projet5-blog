<?php

namespace Application\Logger;

class Logger
{
    private function __construct()
    {
        // Disabled
    }

    public static function addLog(string $data)
    {
        $data = date("Y-m-d H:i:s") . " client: " . ($_SERVER['REMOTE_ADDR'] ?? "") . " message: " . $data;
        file_put_contents(ROOT_PATH . "/log/blog.log", $data . "\n", FILE_APPEND);
    }
}