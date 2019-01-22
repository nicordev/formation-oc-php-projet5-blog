<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 22/01/2019
 * Time: 14:41
 */

namespace Model\Manager;


use Model\Entity\Entity;
use Model\Entity\Post;
use \Exception;
use \PDO;

class PostManager extends Manager
{
    protected static $tableNames = [
        'p_id',
        'p_author_id_fk',
        'p_last_editor_id_fk',
        'p_creation_date',
        'p_last_modification_date',
        'p_title',
        'p_excerpt',
        'p_content'
    ];

    /**
     * Add a new blog post in the database
     *
     * @param Post $newPost
     * @throws Exception
     */
    public function add(Post $newPost) : void
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
    public function edit(Post $modifiedPost) : void
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

    /**
     * Delete a post in the database
     *
     * @param int $postId
     * @throws Exception
     */
    public function delete(int $postId) : void
    {
        $query = 'DELETE FROM bl_post WHERE p_id = ?';

        $requestDelete = $this->database->prepare($query);
        if (!$requestDelete->execute([$postId])) {
            throw new Exception('Error when trying to delete a post in the database. Post id:' . $postId);
        }
    }

    /**
     * Get a post from the database
     *
     * @param int $postId
     * @return Post
     * @throws Exception
     */
    public function get(int $postId) : Post
    {
        $query = "SELECT * FROM bl_post WHERE p_id = ?";

        $requestAPost = $this->database->prepare($query);
        if (!$requestAPost->execute([$postId])) {
            throw new Exception('Error when trying to get a post from the database. Post id:' . $postId);
        }
        $thePostData = $requestAPost->fetch(PDO::FETCH_ASSOC);
        if (!$thePostData) {
            throw new Exception('Error when trying to get a post. Post id: ' . $postId);
        }

        return self::createAPostFromDatabaseData($thePostData);
    }

    public function getAll() : array
    {
        $posts = [];
        $query = "SELECT * FROM bl_post";

        $requestAllPosts = $this->database->query($query);
        $postsData = $requestAllPosts->fetchAll(PDO::FETCH_ASSOC);

        var_dump($postsData);
        die;
    }

    // Private

    /**
     * @param array $data
     * @return Post
     */
    private static function createAPostFromDatabaseData(array $data) : Post
    {
        $attributes = [
            'id' => $data['p_id'],
            'authorId' => $data['p_author_id_fk'],
            'lastEditorId' => $data['p_last_editor_id_fk'] === null ? Entity::NO_ID : $data['p_last_editor_id_fk'],
            'creationDate' => $data['p_creation_date'],
            'lastModificationDate' => $data['p_last_modification_date'] === null ? '' : $data['p_last_modification_date'],
            'title' => $data['p_title'],
            'excerpt' => $data['p_excerpt'],
            'content' => $data['p_content']
        ];

        return new Post($attributes);
    }
}