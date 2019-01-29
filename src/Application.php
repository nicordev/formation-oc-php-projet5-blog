<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 22/01/2019
 * Time: 14:19
 */

namespace Application;


use Controller\BlogController;
use Controller\HomeController;
use Model\Entity\Post;
use Model\Manager\CategoryManager;
use Model\Manager\CommentManager;
use Model\Manager\PostManager;
use Model\Manager\TagManager;
use Twig_Environment;
use Twig_Loader_Filesystem;

class Application
{
    private $twig;

    const VIEW_404 = 'pageNotFound.twig';

    public function __construct()
    {
        $twigLoader = new Twig_Loader_Filesystem(__DIR__ . '/view');

        $this->twig = new Twig_Environment($twigLoader, [
            'debug' => true, // TODO change to false for production
            'cache' => false // TODO change to true for production
        ]);
    }

    public function run()
    {
        $blogController = new BlogController( // A instancier si besoin
            new PostManager(),
            new TagManager(),
            new CategoryManager(),
            new CommentManager(),
            $this->twig
        );

        $homeController = new HomeController(
            new PostManager(),
            new CategoryManager(),
            $this->twig
        );

        // try
        if (isset($_GET['page'])) {
            $page = $_GET['page'];

            // Blog
            if ($page === 'blog') {
                $blogController->showAllPosts();

            // Blog post
            } elseif ($page === 'post' &&
                isset($_GET['post-id']) &&
                is_numeric($_GET['post-id'])) {

                $blogController->showASinglePost($_GET['post-id']);

            // Blog admin
            } elseif ($page === 'blog-admin') {
                if (isset($_POST['add-post'])) {
                    $blogController->addPost();

                } elseif (isset($_POST['edit-post'])) {
                    $blogController->editPost();

                } elseif (isset($_POST['delete-post'])) {
                    $blogController->deletePost();

                } else {
                    $blogController->showAdminPanel();
                }

            // Post editor
            } elseif ($page === 'post-editor') {
                $postId = Post::NO_ID;
                if (isset($_POST['post-id'])) {
                    $postId = (int)$_POST['post-id'];
                }
                $blogController->showPostEditor($postId);

            // Error 404
            } else {
                $this->showError404();
            }

        // Home
        } else {
            $homeController->showHome();
        }
    }

    // Private

    /**
     * Show a page for errors 404
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function showError404()
    {
        echo $this->twig->render(self::VIEW_404);
    }
}