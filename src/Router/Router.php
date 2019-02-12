<?php

namespace Application\Router;

use Controller\BlogController;
use Controller\ErrorController;
use Controller\HomeController;
use Controller\MemberController;

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

            // Home

            case '/home':
                $controller = HomeController::class;
                $method = 'showHome';;
                $params = [];
                break;

            // Blog

            case '/blog':
                $controller = BlogController::class;
                $method = 'showPostsOfACategory';
                $params = ['categoryId' => (int) $_GET['category-id']];
                break;

            case '/blog/tag':
                $controller = BlogController::class;
                $method = 'showPostsOfATag';
                $params = ['tagId' => (int) $_GET['tag-id']];
                break;

            case '/blog-post':
                if (
                    isset($_GET['post-id']) && is_numeric($_GET['post-id'])
                ) {
                    $controller = BlogController::class;
                    $method = 'showASinglePost';
                    $params = [
                        'postId' => $_GET['post-id']
                    ];
                } else {
                    $controller = BlogController::class;
                    $method = 'pageNotFound404';
                    $params = [];
                }
                break;

            // Member

            case '/member-profile':
                $controller = MemberController::class;
                $method = 'showMemberProfile';
                $params = ['memberId' => isset($_GET['id']) ? $_GET['id'] : null];
                break;

            case '/profile-editor':
                $controller = MemberController::class;
                $method = 'showMemberProfileEditor';
                $params = [];
                if (isset($_GET['action']) && !empty($_GET['action'])) {
                    switch ($_GET['action']) {
                        case 'update':
                            $method = 'updateProfile';
                            break;
                        case 'delete':
                            $method = 'deleteMember';
                            $params = ['id' => $_POST['id']];
                            break;
                    }
                }
                break;

            case '/registration':
                $controller = MemberController::class;

                if (isset($_GET['action']) && $_GET['action'] === 'register') {
                    $method = 'register';
                } else {
                    $method = 'showRegistrationPage';
                }

                $params = [];
                break;

            case '/connection':
                $controller = MemberController::class;

                if (isset($_GET['action']) && $_GET['action'] === 'connect') {
                    $method = 'connect';
                } else {
                    $method = 'showConnectionPage';
                }

                $params = [];
                break;

            case '/disconnection':
                $controller = MemberController::class;
                $method = 'disconnect';
                $params = [];
                break;

            // Admin

            case '/admin':
                if (isset($_SESSION['connected-member']) && MemberController::hasAccessToAdminPanel($_SESSION['connected-member'])) {
                    $controller = BlogController::class;
                    $method = 'showAdminPanel';
                    $params = [];
                } else {
                    $controller = MemberController::class;
                    $method = 'showConnectionPage';
                    $params = ['message' => 'Vous devez être connecté et disposer des droits suffisants pour accéder.'];
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

            case '/admin/category-editor':
                if (isset($_POST['category-id'])) {
                    $categoryId = (int) $_POST['category-id'];
                }
                $controller = BlogController::class;
                $method = 'showCategoryEditor';
                $params = isset($categoryId) ? ['categoryId' => $categoryId] : [];
                break;

            case '/admin/add-category':
                $controller = BlogController::class;
                $method = 'addCategory';
                $params = [];
                break;

            case '/admin/edit-category':
                $controller = BlogController::class;
                $method = 'editCategory';
                $params = [];
                break;

            case '/admin/delete-category':
                $controller = BlogController::class;
                $method = 'deleteCategory';
                $params = [];
                break;

            case '/admin/update-tags':
                $controller = BlogController::class;
                $method = 'updateTagList';
                $params = [
                    'tagIds' => $_POST['tag_ids'],
                    'tagNames' => $_POST['tag_names'],
                    'action' => isset($_GET['action']) ? $_GET['action'] : null
                ];
                break;

            default:
                $controller = ErrorController::class;
                $method = 'showError404';
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