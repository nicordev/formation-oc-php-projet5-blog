<?php

use Application\Application;

require_once dirname(__DIR__) . '/vendor/autoload.php';

if (class_exists('\\Symfony\\Component\\Debug\\Debug')) {
    \Symfony\Component\Debug\Debug::enable(E_ERROR);
}

Application::run();