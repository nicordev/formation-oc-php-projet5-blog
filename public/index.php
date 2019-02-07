<?php

/**
 * @param $variables
 */
function varDump($variables)
{
    echo '<pre>';
    if (is_array($variables)) {
        foreach ($variables as $variable) {
            var_dump($variable);
        }
    } else {
        var_dump($variables);
    }
    echo '</pre>';
}

use Application\Application;

require_once('../vendor/autoload.php');

if (class_exists('\\Symfony\\Component\\Debug\\Debug')) {
    \Symfony\Component\Debug\Debug::enable(E_ERROR);
}

$application = new Application();

$application->run();