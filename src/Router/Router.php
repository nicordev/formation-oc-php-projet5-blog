<?php

namespace Application\Router;

class Router
{
    /**
     * Disable the constructor to simulate a static class
     */
    private function __construct()
    {}
    
    /**
     * Analyze the url and return the controller name, the method to call and the parameters
     *
     * @return Route
     */
    public static function run(): Route
    {
        // Default route : Home
        $controller = BlogController::class;
        $method = 'showAllPosts';
        $params = [];

        if (isset($_GET['page'])) {
            $page = $_GET['page'];

            // Blog
            if ($page === 'blog') {
                $controller = BlogController::class;
                $method = 'showAllPosts';
                $params = [];

            // Blog Post
            } elseif ($page === 'blog-post' &&
                isset($_GET['post-id']) &&
                is_numeric($_GET['post-id'])) {

                $controller = BlogController::class;
                $method = 'showASinglePost';
                $params = ['postId' => $_GET['post-id']];

            // Blog Admin
            } elseif ($page === 'blog-admin') {
                if (isset($_POST['add-post'])) {
                    $controller = BlogController::class;
                    $method = 'addPost';
                    $params = [];

                } elseif (isset($_POST['edit-post'])) {
                    $controller = BlogController::class;
                    $method = 'editPost';
                    $params = [];
                } elseif (isset($_POST['delete-post'])) {
                    $controller = BlogController::class;
                    $method = 'deletePost';
                    $params = [];

                } else {
                    $controller = BlogController::class;
                    $method = 'showAdminPanel';
                    $params = [];
                }

            // Post Editor
            } elseif ($page === 'post-editor') {

                if (isset($_POST['post-id'])) {
                    $postId = (int) $_POST['post-id'];
                }

                $controller = BlogController::class;
                $method = 'showPostEditor';
                $params = isset($postId) ? ['postId' => $postId] : [];

            // 404 page not found
            } else {
                // TODO Throw an exception instead
                $controller = BlogController::class;
                $method = 'pageNotFound404';
                $params = [];
            }
        }

        return new Route($controller, $method, $params);
    }
}