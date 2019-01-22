<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 22/01/2019
 * Time: 14:41
 */

namespace Model\Manager;


use Model\Entity\Post;
use mysql_xdevapi\Exception;

class PostManager extends Manager
{
    /**
     * Add a new blog post in the database
     *
     * @param Post $newPost
     */
    public function add(Post $newPost)
    {
        $query = 'INSERT INTO bl_post(p_author_id_fk, p_creation_date, p_title, p_excerpt, p_content)
            VALUES (:authorId, NOW(), :title, :excerpt, :content)';

        $requestAdd = $this->database->prepare($query);
        if (!$requestAdd->execute([
            'authorId' => $newPost->getAuthorId(),
            'title' => $newPost->getTitle(),
            'excerpt' => $newPost->getExcerpt(),
            'content' => $newPost->getContent()
        ])) {
            throw new Exception('Error when trying to add the new blog post in the database.');
        }
    }
}