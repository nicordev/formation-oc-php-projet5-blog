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
        $controller = 'Controller\BlogController';
        $method = 'showAllPosts';
        $params = [];

        if (isset($_GET['page'])) {
            $page = $_GET['page'];

            // Blog
            if ($page === 'blog') {
                $controller = 'Controller\BlogController';
                $method = 'showAllPosts';
                $params = [];

            // Blog Post
            } elseif ($page === 'blog-post' &&
                isset($_GET['post-id']) &&
                is_numeric($_GET['post-id'])) {

                $controller = 'Controller\BlogController';
                $method = 'showASinglePost';
                $params = ['postId' => $_GET['post-id']];

            // Blog Admin
            } elseif ($page === 'blog-admin') {
                if (isset($_POST['add-post'])) {
                    $controller = 'Controller\BlogController';
                    $method = 'addPost';
                    $params = [];

                } elseif (isset($_POST['edit-post'])) {
                    $controller = 'Controller\BlogController';
                    $method = 'editPost';
                    $params = [];
                } elseif (isset($_POST['delete-post'])) {
                    $controller = 'Controller\BlogController';
                    $method = 'deletePost';
                    $params = [];

                } else {
                    $controller = 'Controller\BlogController';
                    $method = 'showAdminPanel';
                    $params = [];
                }

            // Post Editor
            } elseif ($page === 'post-editor') {

                if (isset($_POST['post-id'])) {
                    $postId = (int) $_POST['post-id'];
                }

                $controller = 'Controller\BlogController';
                $method = 'showPostEditor';
                $params = isset($postId) ? ['postId' => $postId] : [];

            // 404 page not found
            } else {
                // TODO Throw an exception instead
                $controller = 'Controller\BlogController';
                $method = 'pageNotFound404';
                $params = [];
            }
        }

        return new Route($controller, $method, $params);
    }
}