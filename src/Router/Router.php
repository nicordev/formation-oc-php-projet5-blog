<?php

namespace Application\Router;

class Router
{
    /**
     * Analyze the url and return the controller name, the method to call and the parameters
     *
     * @return array
     */
    public static function run()
    {
        if (isset($_GET['page'])) {
            $page = $_GET['page'];

            if ($page === 'blog') {
                return self::buildRequestedRoute('BlogController', 'showAllPosts');

            } elseif ($page === 'blog-post' &&
                isset($_GET['post-id']) &&
                is_numeric($_GET['post-id'])) {

                return self::buildRequestedRoute('BlogController', 'showASinglePost', ['postId' => $_GET['post-id']]);

            } elseif ($page === 'blog-admin') {
                if (isset($_POST['add-post'])) {
                    return self::buildRequestedRoute('BlogController', 'addPost');

                } elseif (isset($_POST['edit-post'])) {
                    return self::buildRequestedRoute('BlogController', 'editPost');

                } elseif (isset($_POST['delete-post'])) {
                    return self::buildRequestedRoute('BlogController', 'deletePost');

                } else {
                    return self::buildRequestedRoute('BlogController', 'showAdminPanel');
                }

            } elseif ($page === 'post-editor') {

                if (isset($_POST['post-id'])) {
                    $postId = (int) $_POST['post-id'];
                }
                return self::buildRequestedRoute('BlogController', 'showPostEditor', isset($postId) ? ['postId' => $postId] : null);

            } else {
                // 404 page not found
                return self::buildRequestedRoute('BlogController', 'pageNotFound404'); // TODO Throw an exception instead
            }

        } else {
            // Home
            return self::buildRequestedRoute();
        }
    }

    /**
     * Return an array containing the controller name, the method name and the parameters
     *
     * @param string $controllerName
     * @param string $methodName
     * @param array $params
     * @return array
     */
    private static function buildRequestedRoute(string $controllerName = 'BlogController', string $methodName = 'showAllPosts', array $params = []): array    {
        return [
            "controller" => $controllerName,
            "method" => $methodName,
            "params" => $params
        ];
    }
}