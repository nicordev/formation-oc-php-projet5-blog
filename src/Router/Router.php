<?php

namespace Application\Router;

use Controller\BlogController;
use Controller\HomeController;

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
                $method = 'showPostsOfACategory';
                $params = ['categoryId' => (int) $_GET['category-id']];
                break;

            case '/blog-post':
                if (
                    isset($_GET['post-id']) && is_numeric($_GET['post-id']) &&
                    isset($_GET['category-id']) && is_numeric($_GET['category-id'])
                ) {
                    $controller = BlogController::class;
                    $method = 'showASinglePost';
                    $params = [
                        'postId' => $_GET['post-id'],
                        'categoryId' => $_GET['category-id']
                    ];
                } else {
                    $controller = BlogController::class;
                    $method = 'pageNotFound404';
                    $params = [];
                }
                break;

            case '/admin':
                $controller = BlogController::class;
                $method = 'showAdminPanel';
                $params = [];
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

            default:
                // Default route : Home
                $controller = HomeController::class;
                $method = 'showHome';;
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