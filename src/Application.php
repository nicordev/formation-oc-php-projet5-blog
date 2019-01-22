<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 22/01/2019
 * Time: 14:19
 */

namespace Application;


use Model\Entity\Post;

class Application
{
    public function run()
    {
        $myPost = new Post([
            'id' => 153,
            'title' => 'Titre',
            'excerpt' => 'Extrait',
            'content' => 'Contenu',
            'authorId' => 344,
            'lastEditorId' => 24,
            'creationDate' => 'today',
            'lastModificationDate' => 'tomorrow'
        ]);

        var_dump($myPost);
    }
}