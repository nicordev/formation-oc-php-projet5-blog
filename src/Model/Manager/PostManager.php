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
use Model\Entity\Tag;
use \PDO;
use Application\Exception\BlogException;

class PostManager extends Manager
{
    protected $attributes = [];

    /**
     * Add a new blog post in the database
     *
     * @param Post $newPost
     * @throws BlogException
     */
    public function add(Post $newPost): void
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
            throw new BlogException('Error when trying to add the new blog post in the database.');
        }

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
     */
    public function edit(Post $modifiedPost): void
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
            throw new BlogException('Error when trying to edit a post in the database. Post id:' . $modifiedPost->getId());
        }
    }

    /**
     * Delete a post in the database
     *
     * @param int $postId
     * @throws BlogException
     */
    public function delete(int $postId): void
    {
        $query = 'DELETE FROM bl_post WHERE p_id = ?';

        $requestDelete = $this->database->prepare($query);
        if (!$requestDelete->execute([$postId])) {
            throw new BlogException('Error when trying to delete a post in the database. Post id:' . $postId);
        }
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
        $query = "SELECT * FROM bl_post WHERE p_id = ?";

        $requestAPost = $this->database->prepare($query);
        if (!$requestAPost->execute([$postId])) {
            throw new BlogException('Error when trying to get a post from the database. Post id:' . $postId);
        }
        $thePostData = $requestAPost->fetch(PDO::FETCH_ASSOC);
        if (!$thePostData) {
            throw new BlogException('Error when trying to get a post. Post id: ' . $postId);
        }

        return self::createAPostFromDatabaseData($thePostData);
    }

    /**
     * Get all posts from the database
     *
     * @return array
     */
    public function getAll(): array
    {
        $posts = [];
        $query = "SELECT * FROM bl_post";

        $requestAllPosts = $this->database->query($query);
        $postsData = $requestAllPosts->fetchAll(PDO::FETCH_ASSOC);

        foreach ($postsData as $postsDatum) {
            $posts[] = self::createAPostFromDatabaseData($postsDatum);
        }

        return $posts;
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
            'lastEditorId' => $data['p_last_editor_id_fk'] === null ? Entity::NO_ID : $data['p_last_editor_id_fk'],
            'creationDate' => $data['p_creation_date'],
            'lastModificationDate' => $data['p_last_modification_date'] === null ? '' : $data['p_last_modification_date'],
            'title' => $data['p_title'],
            'excerpt' => $data['p_excerpt'],
            'content' => $data['p_content']
        ];

        return new Post($attributes);
    }

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
}