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
use \PDO;
use Application\Exception\BlogException;

class PostManager extends Manager
{
    public function __construct()
    {
        $this->tableName = 'bl_post';
        $this->fields = [
            'id' => 'p_id',
            'authorId' => 'p_author_id_fk',
            'lastEditorId' => 'p_last_editor_id_fk',
            'creationDate' => 'p_creation_date',
            'lastModificationDate' => 'p_last_modification_date',
            'title' => 'p_title',
            'excerpt' => 'p_excerpt',
            'content' => 'p_content'
        ];

        parent::__construct();
    }

    /**
     * Add a new blog post in the database
     *
     * @param Post $newPost
     * @throws BlogException
     * @throws \ReflectionException
     */
    public function add($newPost): void
    {
        parent::add($newPost);
    }

    /**
     * Edit a blog post in the database
     *
     * @param Post $modifiedPost
     * @throws BlogException
     * @throws \ReflectionException
     */
    public function edit($modifiedPost): void
    {
        parent::edit($modifiedPost);
    }

    /**
     * Delete a post in the database
     *
     * @param int $postId
     * @throws BlogException
     */
    public function delete(int $postId): void
    {
        parent::delete($postId);
    }

    /**
     * Get a post from the database
     *
     * @param int $postId
     * @return Post
     * @throws BlogException
     */
    public function get(int $postId): Post
    {
        return parent::get($postId);
    }

    /**
     * Get all posts from the database
     *
     * @return array
     */
    public function getAll(): array
    {
        return parent::getAll();
    }

    /**
     * Get only the ids of the posts
     *
     * @return array
     */
    public function getAllIds(): array
    {
        $query = 'SELECT p_id FROM bl_post ORDER BY p_id';
        $requestAllId = $this->database->query($query);

        $idsFromDb = $requestAllId->fetchAll(PDO::FETCH_ASSOC);
        $ids = [];

        foreach ($idsFromDb as $idFromDb) {
            $ids[] = $idFromDb['p_id'];
        }

        return $ids;
    }

    // Private

    /**
     * @param array $data
     * @return Post
     */
    private static function createAPostFromDatabaseData(array $data): Post
    {
        $attributes = [
            'id' => $data['p_id'],
            'authorId' => $data['p_author_id_fk'],
            'lastEditorId' => $data['p_last_editor_id_fk'] === null ? null : $data['p_last_editor_id_fk'],
            'creationDate' => $data['p_creation_date'],
            'lastModificationDate' => $data['p_last_modification_date'] === null ? '' : $data['p_last_modification_date'],
            'title' => $data['p_title'],
            'excerpt' => $data['p_excerpt'],
            'content' => $data['p_content']
        ];

        return new Post($attributes);
    }
}