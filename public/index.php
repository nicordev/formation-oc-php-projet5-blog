<?php

use Application\Application;

require_once('../vendor/autoload.php');

if (class_exists('\\Symfony\\Component\\Debug\\Debug')) {
    \Symfony\Component\Debug\Debug::enable(E_ERROR);
}

$application = new Application();

$application->run();