<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 22/01/2019
 * Time: 14:41
 */

namespace Model\Manager;


use Model\Entity\Post;
use \Exception;

class PostManager extends Manager
{
    /**
     * Add a new blog post in the database
     *
     * @param Post $newPost
     * @throws Exception
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

    /**
     * Edit a blog post in the database
     *
     * @param Post $modifiedPost
     * @throws Exception
     */
    public function edit(Post $modifiedPost)
    {
        $query = 'UPDATE bl_post
            SET p_last_editor_id_fk = :lastEditorId,
                p_last_modification_date = NOW(),
                p_title = :title,
                p_excerpt = :excerpt,
                p_content = :content
            WHERE p_id = :id';
        $requestEdit = $this->database->prepare($query);
        if (!$requestEdit->execute([
            'id' => $modifiedPost->getId(),
            'lastEditorId' => $modifiedPost->getLastEditorId(),
            'title' => $modifiedPost->getTitle(),
            'excerpt' => $modifiedPost->getExcerpt(),
            'content' => $modifiedPost->getContent()
        ])) {
            throw new Exception('Error when trying to edit a post in the database. Post id:' . $modifiedPost->getId());
        }
    }
}