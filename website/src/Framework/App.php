<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 08/01/2019
 * Time: 21:28
 */

namespace Framework;


class App
{
    public function run()
    {
        $uri = $_SERVER['REQUEST_URI']; // uri (uniform request identifier). Exemple : /index.html
        if (!empty($uri) && $uri[-1] === '/')
        {
            header('Location: ' . substr($uri, 0, -1));
            header('HTTP/1.1 301 Moved Permanently');
            exit();
        }
        echo 'zog';
    }
}