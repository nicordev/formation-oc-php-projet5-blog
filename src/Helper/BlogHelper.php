<?php

namespace Helper;

use Controller\Controller;
use Exception;
use Michelf\Markdown;
use Model\Entity\Category;
use Model\Entity\Comment;
use Model\Entity\Entity;
use Model\Entity\Post;
use Model\Entity\Tag;
use Model\Manager\PostManager;

class BlogHelper
{
    private function __construct()
    {
        // Disabled
    }
    
    /**
     * Extract names from an array of Tag
     *
     * @param array $tags
     * @return mixed
     */
    public static function getTagNames(array $tags)
    {
        $tagNames = [];
        foreach ($tags as $tag) {
            $tagNames[] = $tag->getName();
        }
        return $tagNames;
    }

    /**
     * Check if an Entity has to be deleted
     *
     * @param Entity $oldEntity
     * @param array $entityIdsToDelete
     * @return bool
     */
    public static function isEntityToDelete(Entity $oldEntity, array $entityIdsToDelete): bool
    {
        foreach ($entityIdsToDelete as $entityIdToDelete) {
            if ($entityIdToDelete === $oldEntity->getId()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Return Tag entities
     *
     * @return array
     */
    public static function getTagsFromForm(): ?array
    {
        $tags = null;

        if (isset($_POST['tags'])) {
            foreach ($_POST['tags'] as $tag) {
                $tags[] = new Tag(['name' => $tag]);
            }
        }

        return $tags;
    }

    /**
     * Return Category entities
     *
     * @return array|null
     */
    public static function getCategoriesFromForm(): ?array
    {
        $categories = null;

        if (isset($_POST['categories'])) {
            foreach ($_POST['categories'] as $category) {
                $categories[] = new Category(['name' => $category]);
            }
        }

        return $categories;
    }

    /**
     * Create a Post from a form (thanks to $_POST)
     * Work for addPost and editPost
     *
     * @return Post|null
     */
    public static function buildPostFromForm(): ?Post
    {
        $post = new Post();

        if (
            isset($_POST['post-title']) && !empty($_POST['post-title']) &&
            isset($_POST['post-excerpt']) && !empty($_POST['post-excerpt']) &&
            isset($_POST['post-content']) && !empty($_POST['post-content']) &&
            isset($_POST['post-author-id'])
        ) {
            // Common
            $post->setTitle($_POST['post-title']);
            $post->setExcerpt($_POST['post-excerpt']);
            $post->setContent($_POST['post-content']);
            $post->setAuthorId($_POST['post-author-id']);

            if (isset($_POST['add-post'])) {
                $post->setCreationDate(date(Controller::MYSQL_DATE_FORMAT));
            }

            // Edit a post
            if (isset($_POST['edit-post'])) {
                $post->setId($_POST['edit-post']);
                $post->setLastModificationDate(date(Controller::MYSQL_DATE_FORMAT));
            }
            if (isset($_POST['post-editor-id'])) {
                $post->setLastEditorId($_POST['post-editor-id']);
            }

            // Tags
            $tags = self::getTagsFromForm();
            if ($tags) {
                $post->setTags($tags);
            }

            // Categories
            $categories = self::getCategoriesFromForm();
            if ($categories) {
                $post->setCategories($categories);
            }

            // Markdown
            if (isset($_POST['markdown-content']) && !empty($_POST['markdown-content'])) {
                $post->setMarkdown(true);
            }

            return $post;

        } else {
            return null;
        }
    }

    /**
     * Create a Category from a form with $_POST
     *
     * @return Category|null
     */
    public static function buildCategoryFromForm(): ?Category
    {
        $category = new Category();

        if (isset($_POST['category-name']) && !empty($_POST['category-name'])) {
            $category->setName(htmlspecialchars($_POST['category-name']));

            // Category to edit
            if (isset($_POST['edit-category'])) {
                $category->setId((int) $_POST['edit-category']);
            }

            return $category;

        } else {
            return null;
        }
    }

    /**
     * Create a Comment from $_POST
     *
     * @return Comment|null
     */
    public static function buildCommentFromForm(): ?Comment
    {
        $comment = new Comment();

        if (isset($_POST['comment-id'])) {
            $comment->setId((int) $_POST['comment-id']);
        }

        if (isset($_POST['editor-id'])) {
            $comment->setLastEditorId((int) $_POST['editor-id']);
        }

        if (isset($_POST['creation-date'])) {
            $comment->setCreationDate($_POST['creation-date']);
        }

        if (isset($_POST['comment-approved'])) {
            $comment->setApproved(true);
        }

        if (isset($_POST['author-id'])) {
            $comment->setAuthorId((int) $_POST['author-id']);
        }

        if (isset($_POST['post-id'])) {
            $comment->setPostId((int) $_POST['post-id']);
        }

        if (isset($_POST['comment'])) {
            $comment->setContent($_POST['comment']);
        }

        if (isset($_POST['parent-id'])) {
            $comment->setParentId((int) $_POST['parent-id']);
        }

        return $comment;
    }

    /**
     * Cut the title and the excerpt of a post if they are too big. Return a message explaining the modifications.
     *
     * @param Post $post
     * @param string $message
     * @return string
     */
    public static function cutPost(Post $post, string $message = '')
    {
        // Title
        if (strlen($post->getTitle()) > PostManager::TITLE_LENGTH) {
            // We cut
            $post->setTitle(substr($post->getTitle(), 0, PostManager::TITLE_LENGTH));
            $message .= "Attention : le titre ne doit pas dépasser " . PostManager::TITLE_LENGTH . " caractères. Il a été coupé.<br>";
        }
        // Excerpt
        if (strlen($post->getExcerpt()) > PostManager::EXCERPT_LENGTH) {
            // We cut
            $post->setExcerpt(substr($post->getExcerpt(), 0, PostManager::EXCERPT_LENGTH));
            $message .= "Attention : l'extrait ne doit pas dépasser " . PostManager::EXCERPT_LENGTH . " caractères. Il a été coupé.<br>";
        }
        // Content
        if (strlen($post->getContent()) > PostManager::CONTENT_LENGTH) {
            // We cut
            $post->setContent(substr($post->getContent(), 0, PostManager::CONTENT_LENGTH));
            $message .= "Attention : le contenu ne doit pas dépasser " . PostManager::CONTENT_LENGTH . " caractères. Il a été coupé.<br>";
        }

        return $message;
    }

    /**
     * Convert markdown content
     *
     * @param string $content
     * @return string
     */
    public static function convertMarkdown(string $content)
    {
        return Markdown::defaultTransform($content);
    }

    /**
     * Prepare a post before showing it (convert dates and markdown contents)
     *
     * @param Post $post
     * @throws Exception
     */
    public static function prepareAPost(Post $post)
    {
        self::convertDatesOfPost($post);
        if ($post->isMarkdown() && !empty($post->getContent())) {
            $post->setContent(BlogHelper::convertMarkdown($post->getContent()));
        }
    }

    /**
     * Change the date format use in a post
     *
     * @param Post $post
     * @throws Exception
     */
    public static function convertDatesOfPost(Post $post)
    {
        $post->setCreationDate(Controller::formatDate($post->getCreationDate()));

        if ($post->getLastModificationDate() !== null) {
            $post->setLastModificationDate(Controller::formatDate($post->getLastModificationDate()));
        }
    }

    /**
     * Change the date format use in a comment
     *
     * @param Comment $comment
     * @throws Exception
     */
    public static function convertDatesOfComment(Comment $comment)
    {
        $comment->setCreationDate(Controller::formatDate($comment->getCreationDate()));

        if ($comment->getLastModificationDate() !== null) {
            $comment->setLastModificationDate(Controller::formatDate($comment->getLastModificationDate()));
        }
    }
}