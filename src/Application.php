<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 22/01/2019
 * Time: 14:19
 */

namespace Application;


use Controller\BlogController;
use Model\Manager\CategoryManager;
use Model\Manager\CommentManager;
use Model\Manager\PostManager;
use Model\Manager\TagManager;

class Application
{
    public function run()
    {
        $blogController = new BlogController(
            new PostManager(),
            new TagManager(),
            new CategoryManager(),
            new CommentManager(),
            __DIR__ . '/view'
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

                } else {
                    $blogController->showAdminPanel();
                }

            } elseif ($page === 'create-post') {
                $blogController->showCreatePostPanel();

            } else {
                $blogController->pageNotFound404();
            }

        } else {
            $blogController->showAllPosts();
        }
    }
}