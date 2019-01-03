<?php

require_once ('vendor/autoload.php');

use App\Model\Article; // Sans cette ligne, il faudrait écrire $myArticle = new App\Model\Article();

$myArticle = new Article();

var_dump($myArticle);