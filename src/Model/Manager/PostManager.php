<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 22/01/2019
 * Time: 14:41
 */

namespace Model\Manager;


use Model\Entity\Post;
use \PDO;
use Application\Exception\HttpException;

class PostManager extends Manager
{
    public const EXCERPT_LENGTH = 300;
    public const TITLE_LENGTH = 100;
    public const CONTENT_LENGTH = 300000;

    private const KEY_CONTENT = "content";
    private const KEY_POST_ID = "postId";
    private const KEY_CATEGORY_ID = "categoryId";

    /**
     * PostManager constructor.
     * @throws HttpException
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
            'markdown' => 'p_markdown',
            'title' => 'p_title',
            'excerpt' => 'p_excerpt',
            self::KEY_CONTENT => 'p_content'
        ];

        parent::__construct();
    }

    /**
     * Add a new blog post in the database
     *
     * @param Post $newPost
     * @throws HttpException
     * @throws \ReflectionException
     */
    public function add($newPost): void
    {
        parent::add($newPost);

        $newPost->setId($this->getLastId());
        
        // Associate categories and post
        $categories = $newPost->getCategories();

        if (!empty($categories)) {
            $this->associatePostAndCategories($newPost, $categories);
        }
        
        // Associate tags and post
        $tags = $newPost->getTags();
        if (!empty($tags)) {
            $this->associatePostAndTags($newPost, $tags);
        }
    }

    /**
     * Edit a blog post in the database
     *
     * @param Post $modifiedPost
     * @throws HttpException
     * @throws \ReflectionException
     */
    public function edit($modifiedPost): void
    {
        parent::edit($modifiedPost);

        // Associate categories and post
        $categories = $modifiedPost->getCategories();
        $this->associatePostAndCategories($modifiedPost, $categories);

        // Associate tags and post
        $tags = $modifiedPost->getTags();
        $this->associatePostAndTags($modifiedPost, $tags);
    }

    /**
     * Get a post from the database
     *
     * @param int $postId
     * @return Post
     * @throws HttpException
     */
    public function get(int $postId): Post
    {
        $post = parent::get($postId);

        // Tags
        $associatedTags = $this->getTagsOfAPost($post->getId());
        $post->setTags($associatedTags);

        // Categories
        $associatedCategories = $this->getCategoriesOfAPost($post->getId());
        $post->setCategories($associatedCategories);

        // Author and editor
        $this->setMembersOfAPost($post);

        return $post;
    }

    /**
     * Get associated tags of a post
     *
     * @param int $postId
     * @return array
     * @throws HttpException
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
            self::KEY_POST_ID => $postId
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
     * @throws HttpException
     */
    public function getCategoriesOfAPost(int $postId): array
    {
        $categories = [];

        $query = 'SELECT DISTINCT * FROM bl_category
            WHERE cat_id IN (
                SELECT DISTINCT pc_category_id_fk FROM bl_post_category
                WHERE pc_post_id_fk = :postId
            )';

        $requestCategories = $this->query($query, [self::KEY_POST_ID => $postId]);

        while ($categoryData = $requestCategories->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = $this->createEntityFromTableData($categoryData, 'Category');
        }

        return $categories;
    }

    /**
     * Set the author and editor names of a post
     *
     * @param Post $post
     * @throws HttpException
     */
    public function setMembersOfAPost(Post $post)
    {
        // Author
        $authorName = $this->getMemberName($post->getAuthorId());
        $post->setAuthorName($authorName);

        // Editor
        if ($post->getLastEditorId() !== null) {
            $editorName = $this->getMemberName($post->getLastEditorId());
            $post->setEditorName($editorName);
        }
    }

    /**
     * Get all posts from the database
     *
     * @param int|null $numberOfLines
     * @param int|null $start
     * @return array
     * @throws HttpException
     */
    public function getAll(?int $numberOfLines = null, ?int $start = null): array
    {
        $posts = parent::getAll($numberOfLines, $start);

        // Set tags, categories, author name and editor name
        foreach ($posts as $post) {
            $post->setTags($this->getTagsOfAPost($post->getId()));
            $post->setCategories($this->getCategoriesOfAPost($post->getId()));
            $this->setMembersOfAPost($post);
        }

        return $posts;
    }

    /**
     * Get the posts associated to a category via its tags
     *
     * @param int $categoryId
     * @param int|null $numberOfLines
     * @param int|null $start
     * @param bool $withContent
     * @return array
     * @throws HttpException
     */
    public function getPostsOfACategory(int $categoryId, ?int $numberOfLines = null, ?int $start = null, bool $withContent = false)
    {
        $posts = [];
        if ($withContent) {
            $columns = '*';
        } else {
            $columns = $this->fields;
            unset($columns[self::KEY_CONTENT]);
            $columns = implode(', ', $columns);
        }

        $query = 'SELECT ' . $columns . ' FROM ' . $this->tableName . '
            WHERE p_id IN (
                SELECT DISTINCT pc_post_id_fk FROM bl_post_category
                WHERE pc_category_id_fk = :categoryId
            )
            ORDER BY p_last_modification_date DESC, p_creation_date DESC';
        if ($numberOfLines) {
            self::addLimitToQuery($query, $numberOfLines, $start);
        }

        $requestPosts = $this->query($query, [
            self::KEY_CATEGORY_ID => $categoryId
        ]);

        while ($postData = $requestPosts->fetch(PDO::FETCH_ASSOC)) {
            $postData['p_content'] = 'Excerpt only';
            $post = $this->createEntityFromTableData($postData);
            $this->setMembersOfAPost($post);
            $posts[] = $post;
        }

        return $posts;
    }

    /**
     * Get all the posts associated to a given tag
     *
     * @param int $tagId
     * @param int|null $numberOfLines
     * @param int|null $start
     * @param bool $withContent
     * @return array
     * @throws HttpException
     */
    public function getPostsOfATag(int $tagId, ?int $numberOfLines = null, ?int $start = null, bool $withContent = false)
    {
        $posts = [];
        if ($withContent) {
            $columns = '*';
        } else {
            $columns = $this->fields;
            unset($columns[self::KEY_CONTENT]);
            $columns = implode(', ', $columns);
        }

        $query = 'SELECT ' . $columns . ' FROM ' . $this->tableName . '
            WHERE p_id IN (
                SELECT pt_post_id_fk FROM bl_post_tag
                WHERE pt_tag_id_fk = :id
            )
            ORDER BY p_last_modification_date DESC, p_creation_date DESC';
        if ($numberOfLines) {
            self::addLimitToQuery($query, $numberOfLines, $start);
        }

        $requestPosts = $this->query($query, [
            'id' => $tagId
        ]);

        while ($postData = $requestPosts->fetch(PDO::FETCH_ASSOC)) {
            $post = $this->createEntityFromTableData($postData);
            $this->setMembersOfAPost($post);
            $posts[] = $post;
        }

        return $posts;
    }

    /**
     * Get the posts written by a member
     *
     * @param int $memberId
     * @param bool $getContent
     * @param bool $filterWithTags
     * @param int|null $numberOfPosts
     * @param int|null $start
     * @return array
     * @throws HttpException
     */
    public function getPostsOfAMember(int $memberId, bool $getContent = false, bool $filterWithTags = true, ?int $numberOfPosts = null, ?int $start = null): array
    {
        $posts = [];
        if ($getContent) {
            $columns = '*';
        } else {
            $columns = $this->fields;
            unset($columns[self::KEY_CONTENT]);
            $columns = implode(', ', $columns);
        }

        $query = 'SELECT ' . $columns . ' FROM bl_post WHERE p_author_id_fk = :memberId';
        if ($numberOfPosts) {
            self::addLimitToQuery($query, $numberOfPosts, $start);
        }
        $requestPosts = $this->query($query, ['memberId' => $memberId]);

        if ($filterWithTags) {
            while ($postData = $requestPosts->fetch(PDO::FETCH_ASSOC)) {
                $post = $this->createEntityFromTableData($postData, 'Post');
                $post->setTags($this->getTagsOfAPost($post->getId()));
                if ($post->getTags()) {
                    $posts[] = $post;
                }
            }

        } else {
            while ($postData = $requestPosts->fetch(PDO::FETCH_ASSOC)) {
                $post = $this->createEntityFromTableData($postData, 'Post');
                $post->setTags($this->getTagsOfAPost($post->getId()));
                $posts[] = $post;
            }
        }

        return $posts;
    }

    /**
     * Count the number of posts of a category
     *
     * @param int $categoryId
     * @return int
     * @throws HttpException
     */
    public function countPostsOfACategory(int $categoryId): int
    {
        $query = 'SELECT COUNT(p_id) FROM bl_post WHERE p_id IN (
            SELECT pc_post_id_fk FROM bl_post_category WHERE pc_category_id_fk = :categoryId
        )';

        $requestCount = $this->query($query, [self::KEY_CATEGORY_ID => $categoryId]);

        return (int) $requestCount->fetch(PDO::FETCH_NUM)[0];
    }

    /**
     * Count the number of posts of a tag
     *
     * @param int $tagId
     * @return int
     * @throws HttpException
     */
    public function countPostsOfATag(int $tagId): int
    {
        $query = 'SELECT COUNT(p_id) FROM bl_post WHERE p_id IN (
            SELECT pt_post_id_fk FROM bl_post_tag WHERE pt_tag_id_fk = :tagId
        )';

        $requestCount = $this->query($query, ['tagId' => $tagId]);

        return (int) $requestCount->fetch(PDO::FETCH_NUM)[0];
    }

    // Private

    /**
     * Fill the table bl_post_category
     *
     * @param Post $post
     * @param array $categories
     * @throws HttpException
     */
    private function associatePostAndCategories(Post $post, array $categories)
    {
        // Delete
        $query = 'DELETE FROM bl_post_category WHERE pc_post_id_fk = :postId';
        
        $this->query($query, [self::KEY_POST_ID => $post->getId()]);

        if (!empty($categories)) {
            // Add
            $query = 'INSERT INTO bl_post_category(pc_post_id_fk, pc_category_id_fk)
                VALUES (:postId, :categoryId)';
            $requestAdd = $this->database->prepare($query);

            foreach ($categories as $category) {
                $requestAdd->execute([
                    self::KEY_POST_ID => $post->getId(),
                    self::KEY_CATEGORY_ID => $category->getId()
                ]);
            }
        }
    }

    /**
     * Fill the table bl_post_tag
     *
     * @param Post $post
     * @param array $tags
     * @throws HttpException
     */
    private function associatePostAndTags(Post $post, array $tags)
    {
        // Delete
        $query = 'DELETE FROM bl_post_tag WHERE pt_post_id_fk = :postId';

        $this->query($query, [self::KEY_POST_ID => $post->getId()]);

        if (!empty($tags)) {
            // Add
            $query = 'INSERT INTO bl_post_tag(pt_post_id_fk, pt_tag_id_fk)
                VALUES (:postId, :tagId)';
            $requestAdd = $this->database->prepare($query);

            foreach ($tags as $tag) {
                $requestAdd->execute([
                    self::KEY_POST_ID => $post->getId(),
                    'tagId' => $tag->getId()
                ]);
            }
        }
    }

    /**
     * Get the name of a member
     *
     * @param int $memberId
     * @return mixed
     * @throws HttpException
     */
    private function getMemberName(?int $memberId)
    {
        if ($memberId) {
            $query = 'SELECT m_name FROM bl_member WHERE m_id = :id';

            $requestMemberName = $this->query($query, ['id' => $memberId]);
            $memberNameData = $requestMemberName->fetch(PDO::FETCH_NUM);

            return $memberNameData[0];
        }
        return "Un ancien membre qui n'est plus des nôtres";
    }
}
