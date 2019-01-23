<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 14/01/2019
 * Time: 13:34
 */

if (!isset($pageTitle)) {
    $pageTitle = 'Mon super site';
}

// Header
if (!isset($header)) {
    ob_start();
    ?>
    <h1>Mon super site</h1>
    <?php
    $header = ob_get_clean();
}

// Content
if (!isset($content)) {
    ob_start();
    ?>
    <p>Circulez ya rien à voir...</p>
    <?php
    $content = ob_get_clean();
}

// Footer
if (!isset($footer)) {
    ob_start();
    ?>
    <p>Ceci est un footer.</p>
    <?php
    $footer = ob_get_clean();
}

require 'template/layout.php';