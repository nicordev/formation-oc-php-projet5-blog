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
        $controller = 'Controller\HomeController';
        $method = 'showHome';
        $params = [];

        if (isset($_GET['page'])) {
            $page = $_GET['page'];

            // Blog
            if ($page === 'blog') {
                $controller = 'Controller\BlogController';
                $method = 'showAllPosts';
                $params = [];

            // Post
            } elseif ($page === 'post' &&
                isset($_GET['post-id']) &&
                is_numeric($_GET['post-id'])) {

                $controller = 'Controller\BlogController';
                $method = 'showASinglePost';
                $params = ['postId' => $_GET['post-id']];

            // Blog Admin
            } elseif ($page === 'blog-admin') {

                // Manage post
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

                // Manage tags
                } elseif (
                    isset($_POST['action']) && $_POST['action'] === 'update-tag-list'
                ) {
                    $controller = 'Controller\BlogController';
                    $method = 'updateTagList';
                    if (
                        isset($_POST['tag_ids']) &&
                        isset($_POST['tag_names'])
                    ) {
                        $params = [
                            'tagIds' => $_POST['tag_ids'],
                            'tagNames' => $_POST['tag_names']
                        ];
                    } else {
                        $params = [
                            'tagIds' => null,
                            'tagNames' => null
                        ];
                    }

                // Manage categories
                } elseif (isset($_POST['add-category'])) {
                    $controller = 'Controller\BlogController';
                    $method = 'addCategory';
                    $params = [];

                } elseif (isset($_POST['edit-category'])) {
                    $controller = 'Controller\BlogController';
                    $method = 'editCategory';
                    $params = [];

                } elseif (isset($_POST['delete-category'])) {
                    $controller = 'Controller\BlogController';
                    $method = 'deleteCategory';
                    $params = [];
                    
                } else {
                    $controller = 'Controller\BlogController';
                    $method = 'showAdminPanel';
                    $params = [];
                }

            // Post Editor
            } elseif ($page === 'post-editor') {

                if (isset($_POST['post-id'])) {
                    $postId = (int)$_POST['post-id'];
                }

                $controller = 'Controller\BlogController';
                $method = 'showPostEditor';
                $params = isset($postId) ? ['postId' => $postId] : [];

            // Category editor
            } elseif ($page === 'category-editor')  {

                if (isset($_POST['category-id'])) {
                    $categoryId = (int) $_POST['category-id'];
                }

                $controller = 'Controller\BlogController';
                $method = 'showCategoryEditor';
                $params = isset($categoryId) ? ['categoryId' => $categoryId] : [];

            // 404 page not found
            } else {
                // TODO Throw an exception instead
                $controller = 'Controller\ErrorController';
                $method = 'showError404';
                $params = [];
            }
        }

        return new Route($controller, $method, $params);
    }
}