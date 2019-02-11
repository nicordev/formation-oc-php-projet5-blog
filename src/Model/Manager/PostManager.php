<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 22/01/2019
 * Time: 14:41
 */

namespace Model\Manager;


use Model\Entity\Category;
use Model\Entity\Post;
use Model\Entity\Tag;
use \PDO;
use Application\Exception\BlogException;

class PostManager extends Manager
{
    public const EXCERPT_LENGTH = 300;
    public const TITLE_LENGTH = 100;

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
     * Edit a blog post in the database
     *
     * @param Post $modifiedPost
     * @throws BlogException
     * @throws \ReflectionException
     */
    public function edit($modifiedPost): void
    {
        parent::edit($modifiedPost);

        // Associate tags and post
        $tags = $modifiedPost->getTags();
        if (!empty($tags)) {
            $this->associatePostAndTags($modifiedPost, $tags);
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
     * @throws BlogException
     */
    public function getTagsOfAPost(int $postId): array
    {
        $tags = [];

        $query = 'SELECT bl_tag.* FROM bl_tag
            INNER JOIN bl_post_tag
                ON tag_id = pt_tag_id_fk
            INNER JOIN bl_post ON pt_post_id_fk = p_id
            WHERE p_id = :postId';

        $requestTags = $this->query($query, [
            'postId' => $postId
        ]);

        while ($tagData = $requestTags->fetch(PDO::FETCH_ASSOC)) {
            $tags[] = $this->createEntityFromTableData($tagData, 'Tag');
        }

        return $tags;
    }

    /**
     * Get associated categories of a post
     *
     * @param int $postId
     * @return array
     * @throws BlogException
     */
    public function getCategoriesOfAPost(int $postId): array
    {
        $categories = [];

        $query = 'SELECT DISTINCT * FROM bl_category
            WHERE cat_id IN (
                SELECT DISTINCT ct_category_id_fk FROM bl_category_tag
                WHERE ct_tag_id_fk IN (
                    SELECT DISTINCT pt_tag_id_fk FROM bl_post_tag
                    WHERE pt_post_id_fk = :id
                )
            )';

        $requestCategories = $this->query($query, ['id' => $postId]);

        while ($categoryData = $requestCategories->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = $this->createEntityFromTableData($categoryData, 'Category');
        }

        return $categories;
    }

    /**
     * Get all posts from the database
     *
     * @return array
     * @throws BlogException
     */
    public function getAll(): array
    {
        $posts = parent::getAll();

        // Set tags and categories
        foreach ($posts as $post) {
            $post->setTags($this->getTagsOfAPost($post->getId()));
            $post->setCategories($this->getCategoriesOfAPost($post->getId()));
        }

        return $posts;
    }

    /**
     * Get only the ids of the posts
     *
     * @param int|null $categoryId
     * @return array
     * @throws BlogException
     */
    public function getAllIds(?int $categoryId = null): array
    {
        if ($categoryId === null) {
            $query = 'SELECT ' . $this->fields['id'] . ' FROM ' . $this->tableName . ' ORDER BY ' . $this->fields['id'];

            $requestAllIds = $this->query($query);

        } else {
            $query = 'SELECT p_id FROM bl_post
                WHERE p_id IN (
                    SELECT DISTINCT pt_post_id_fk FROM bl_post_tag
                    WHERE pt_tag_id_fk IN (
                        SELECT tag_id FROM bl_tag
                            INNER JOIN bl_category_tag
                                ON tag_id = ct_tag_id_fk
                            INNER JOIN bl_category
                                ON cat_id = ct_category_id_fk
                        WHERE cat_id = :id)
                )';

            $requestAllIds = $this->query($query, [
                'id' => $categoryId
            ]);
        }

        $idsFromDb = $requestAllIds->fetchAll(PDO::FETCH_ASSOC);
        $ids = [];

        foreach ($idsFromDb as $idFromDb) {
            $ids[] = $idFromDb['p_id'];
        }

        return $ids;
    }

    /**
     * Get the posts associated to a category via its tags
     *
     * @param int $categoryId
     * @param bool $withContent
     * @return array
     * @throws BlogException
     */
    public function getPostsOfACategory(int $categoryId, bool $withContent = false)
    {
        $posts = [];
        if ($withContent) {
            $columns = '*';
        } else {
            $columns = 'p_id, p_excerpt, p_last_modification_date, p_last_editor_id_fk, p_author_id_fk, p_creation_date, p_title';
        }

        $query = 'SELECT ' . $columns . ' FROM bl_post
            WHERE p_id IN (
                SELECT DISTINCT pt_post_id_fk FROM bl_post_tag
                WHERE pt_tag_id_fk IN (
                    SELECT tag_id FROM bl_tag
                        INNER JOIN bl_category_tag
                            ON tag_id = ct_tag_id_fk
                        INNER JOIN bl_category
                            ON cat_id = ct_category_id_fk
                    WHERE cat_id = :id) # Use the requested category id here
            )';
        $requestPosts = $this->query($query, [
            'id' => $categoryId
        ]);

        while ($postData = $requestPosts->fetch(PDO::FETCH_ASSOC)) {
            $postData['p_content'] = 'Excerpt only';
            $posts[] = $this->createEntityFromTableData($postData);
        }

        return $posts;
    }

    /**
     * Get all the posts associated to a given tag
     *
     * @param int $tagId
     * @return array
     * @throws BlogException
     */
    public function getPostsOfATag(int $tagId)
    {
        $posts = [];

        $query = 'SELECT * FROM bl_post
            WHERE p_id IN (
                SELECT pt_post_id_fk FROM bl_post_tag
                WHERE pt_tag_id_fk = :id
            )';

        $requestPosts = $this->query($query, [
            'id' => $tagId
        ]);

        while ($postData = $requestPosts->fetch(PDO::FETCH_ASSOC)) {
            $posts[] = $this->createEntityFromTableData($postData);
        }

        return $posts;
    }

    // Private

    /**
     * Fill the table bl_post_tag
     *
     * @param Post $post
     * @param array $tags
     * @throws BlogException
     */
    private function associatePostAndTags(Post $post, array $tags)
    {
        // Delete
        $query = 'DELETE FROM bl_post_tag WHERE pt_post_id_fk = :postId';

        $this->query($query, ['postId' => $post->getId()]);

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