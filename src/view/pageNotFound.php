<?php
$pageTitle = 'Page introuvable';

ob_start();
?>
    <h2>Page introuvable...</h2>

    <p><a href="index.php?page=blog">Revenir à la liste des articles</a></p>
<?php

$content = ob_get_clean();

require 'main.php';