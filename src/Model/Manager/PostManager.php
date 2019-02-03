<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 22/01/2019
 * Time: 14:41
 */

namespace Model\Manager;


use Model\Entity\Post;
use Model\Entity\Tag;
use \PDO;
use Application\Exception\BlogException;

class PostManager extends Manager
{
    /**
     * PostManager constructor.
     */
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

        // Associate tags and post
        $tags = $newPost->getTags();
        if (!empty($tags)) {
            $newPost->setId($this->getLastId());
            $this->associatePostAndTags($newPost, $tags);
        }
    }

    /**
     * Get the last post id.
     *
     * @return int
     */
    public function getLastId(): int
    {
        $query = 'SELECT MAX(p_id) FROM bl_post';
        $requestLastId = $this->database->query($query);

        $lastId = (int) $requestLastId->fetch(PDO::FETCH_NUM)[0];

        return $lastId;
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
        $post = parent::get($postId);

        $associatedTags = $this->getTagsOfAPost($post->getId());
        $post->setTags($associatedTags);

        return $post;
    }

    /**
     * Get associated tags of a post
     *
     * @param int $postId
     * @return array
     */
    public function getTagsOfAPost(int $postId)
    {
        $tags = [];

        $query = 'SELECT bl_tag.* FROM bl_tag
            INNER JOIN bl_post_tag
                ON tag_id = pt_tag_id_fk
            INNER JOIN bl_post ON pt_post_id_fk = p_id
            WHERE p_id = :postId';

        $requestTags = $this->database->prepare($query);
        $requestTags->execute([
            'postId' => $postId
        ]);
        while ($tagData = $requestTags->fetch(PDO::FETCH_ASSOC)) {
            $tags[] = TagManager::createATagFromDatabaseData($tagData);
        }

        return $tags;
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
     * Fill the table bl_post_tag
     *
     * @param Post $post
     * @param array $tags
     */
    private function associatePostAndTags(Post $post, array $tags)
    {
        // Delete
        $query = 'DELETE FROM bl_post_tag WHERE pt_post_id_fk = :postId';
        $requestDelete = $this->database->prepare($query);
        $requestDelete->execute([
            'postId' => $post->getId()
        ]);

        // Add
        $query = 'INSERT INTO bl_post_tag(pt_post_id_fk, pt_tag_id_fk)
                VALUES (:postId, :tagId)';
        $requestAdd = $this->database->prepare($query);

        foreach ($tags as $tag) {
            $requestAdd->execute([
                'postId' => $post->getId(),
                'tagId' => $tag->getId()
            ]);
        }
    }

    // Old

    /**
     * @param array $data
     * @return Post
     */
    private static function createAPostFromDatabaseData(array $data): Post
    {
        $attributes = [
            'id' => $data['p_id'],
            'authorId' => $data['p_author_id_fk'],
            'lastEditorId' => $data['p_last_editor_id_fk'],
            'creationDate' => $data['p_creation_date'],
            'lastModificationDate' => $data['p_last_modification_date'],
            'title' => $data['p_title'],
            'excerpt' => $data['p_excerpt'],
            'content' => $data['p_content']
        ];

        return new Post($attributes);
    }
}