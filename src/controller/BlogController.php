<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 23/01/2019
 * Time: 09:03
 */

namespace Controller;


use Application\Exception\BlogException;
use Exception;
use Model\Entity\Post;
use Model\Manager\CategoryManager;
use Model\Manager\CommentManager;
use Model\Manager\PostManager;
use Model\Manager\TagManager;

class BlogController
{
    protected $postManager;
    protected $tagManager;
    protected $categoryManager;
    protected $commentManager;
    protected $viewFolderPath = '';

    public function __construct(PostManager $postManager,
                                TagManager $tagManager,
                                CategoryManager $categoryManager,
                                CommentManager $commentManager,
                                string $viewFolderPath)
    {
        $this->postManager = $postManager;
        $this->tagManager = $tagManager;
        $this->categoryManager = $categoryManager;
        $this->commentManager = $commentManager;
        $this->viewFolderPath = $viewFolderPath;
    }

    /**
     * Show all posts of the blog
     */
    public function showAllPosts()
    {
        $posts = $this->postManager->getAll();

        require $this->viewFolderPath . '/blogPostsListing.php';
    }

    /**
     * Show an entire blog post
     *
     * @param $postId
     * @return void
     * @throws Exception
     */
    public function showASinglePost(int $postId)
    {
        try {
            $post = $this->postManager->get($postId);
            $nextPostId = $this->getNextPostId($postId);
            $previousPostId = $this->getPreviousPostId($postId);

        } catch (BlogException $e) {
            require $this->viewFolderPath . '/postNotFound.php';
            exit();
        }

        require $this->viewFolderPath . '/blogPost.php';
    }

    /**
     * Get the next Post id or false if it's the last post
     *
     * @param int $postId
     * @return bool
     */
    private function getNextPostId(int $postId)
    {
        $ids = $this->postManager->getAllIds();

        for ($i = 0, $size = count($ids); $i < $size; $i++) {
            if ($postId < $ids[$i]) {
                return $ids[$i];
            }
        }

        return false;
    }

    /**
     * Get the previous Post id or false if it's the first post
     *
     * @param int $postId
     * @return bool
     */
    private function getPreviousPostId(int $postId)
    {
        $ids = $this->postManager->getAllIds();

        for ($i = count($ids) - 1; $i >= 0; $i--) {
            if ($postId > $ids[$i]) {
                return $ids[$i];
            }
        }

        return false;
    }
}