<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 22/01/2019
 * Time: 14:19
 */

namespace Application;


use Model\Manager\CategoryManager;
use Model\Manager\PostManager;
use Model\Manager\TagManager;

class Application
{
    public function run()
    {
        $postManager = new PostManager();
        $tagManager = new TagManager();
        $categoryManager = new CategoryManager();
    }
}