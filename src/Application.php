<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 22/01/2019
 * Time: 14:19
 */

namespace Application;


use Model\Entity\Post;
use Model\Manager\PostManager;

class Application
{
    public function run()
    {
        $postManager = new PostManager();

        $posts = $postManager->getAll();

        var_dump($posts);
    }
}