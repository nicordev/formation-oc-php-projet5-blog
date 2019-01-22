<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 14/01/2019
 * Time: 13:34
 */

// Header
ob_start();
?>
    <h1>Blog</h1>
<?php
$header = ob_get_clean();

// Content
ob_start();
?>

<?php
$content = ob_get_clean();

// Footer
ob_start();
?>

<?php
$footer = ob_get_clean();

require 'template/layout.php';