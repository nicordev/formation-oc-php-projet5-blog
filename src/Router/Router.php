<?php

namespace Application\Router;

use Controller\BlogController;

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
        $url = self::getUrl();

        switch ($url) {

            case '/blog':
                $controller = BlogController::class;
                $method = 'showAllPosts';
                $params = [];
                break;

            case '/blog-post':
                if (
                    isset($_GET['post-id']) &&
                    is_numeric($_GET['post-id'])
                ) {
                    $controller = BlogController::class;
                    $method = 'showASinglePost';
                    $params = ['postId' => $_GET['post-id']];
                } else {
                    $controller = BlogController::class;
                    $method = 'pageNotFound404';
                    $params = [];
                }
                break;

            case '/admin':

                // Manage tags
                if (isset($_POST['tag_ids']) &&
                    isset($_POST['tag_names'])
                ) {
                    $controller = 'Controller\BlogController';
                    $method = 'updateTagList';
                    $params = [
                        'tagIds' => $_POST['tag_ids'],
                        'tagNames' => $_POST['tag_names']
                    ];
                } else {
                    $controller = BlogController::class;
                    $method = 'showAdminPanel';
                    $params = [];
                }
                break;

            case '/admin/add-post':
                $controller = BlogController::class;
                $method = 'addPost';
                $params = [];
                break;

            case '/admin/edit-post':
                $controller = BlogController::class;
                $method = 'editPost';
                $params = [];
                break;

            case '/admin/delete-post':
                $controller = BlogController::class;
                $method = 'deletePost';
                $params = [];
                break;

            case '/admin/post-editor':
                if (isset($_POST['post-id'])) {
                    $postId = (int) $_POST['post-id'];
                }
                $controller = BlogController::class;
                $method = 'showPostEditor';
                $params = isset($postId) ? ['postId' => $postId] : [];
                break;

            case '/admin/update-tags':
                $controller = BlogController::class;
                $method = 'updateTagList';
                $params = [
                    'tagIds' => $_POST['tag_ids'],
                    'tagNames' => $_POST['tag_names']
                ];
                break;

            default:
                // Default route : Home
                $controller = BlogController::class;
                $method = 'showAllPosts';
                $params = [];
                break;
        }

        return new Route($controller, $method, $params);
    }

    // Private

    /**
     * Get the url
     *
     * @return mixed
     */
    private static function getUrl()
    {
        $urlParts = explode('?', $_SERVER['REQUEST_URI']);

        return $urlParts[0];
    }
}