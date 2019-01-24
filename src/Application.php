<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 22/01/2019
 * Time: 14:19
 */

namespace Application;


use Controller\BlogController;
use Model\Entity\Post;
use Model\Manager\CategoryManager;
use Model\Manager\CommentManager;
use Model\Manager\PostManager;
use Model\Manager\TagManager;
use Twig_Environment;
use Twig_Loader_Filesystem;

class Application
{
    public function run()
    {
        $twigLoader = new Twig_Loader_Filesystem(__DIR__ . '/view');

        $twig = new Twig_Environment($twigLoader, [
            'debug' => true, // TODO change to false for production
            'cache' => false // TODO change to true for production
        ]);

        $blogController = new BlogController(
            new PostManager(),
            new TagManager(),
            new CategoryManager(),
            new CommentManager(),
            $twig
        );

        if (isset($_GET['page'])) {
            $page = $_GET['page'];

            if ($page === 'blog') {
                $blogController->showAllPosts();

            } elseif ($page === 'blog-post' &&
                isset($_GET['post-id']) &&
                is_numeric($_GET['post-id'])) {

                $blogController->showASinglePost($_GET['post-id']);

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

            } elseif ($page === 'post-editor') {
                $postId = Post::NO_ID;
                if (isset($_POST['post-id'])) {
                    $postId = (int)$_POST['post-id'];
                }
                $blogController->showPostEditor($postId);

            } else {
                $blogController->pageNotFound404();
            }

        } else {
            $blogController->showAllPosts();
        }
    }
}