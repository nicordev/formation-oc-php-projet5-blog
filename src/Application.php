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
        $myPost = new Post([
            'id' => 2,
            'title' => 'Titre',
            'excerpt' => 'Extrait',
            'content' => 'Contenu',
            'authorId' => 1,
            'lastEditorId' => 2
        ]);

        $postManager = new PostManager();

        $myPost = $postManager->get(3);

        var_dump($myPost);
    }
}