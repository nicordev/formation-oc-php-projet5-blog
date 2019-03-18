<?php

namespace Application\Router;

use Application\Exception\PageNotFoundException;
use Application\Security\CsrfProtector;
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
     * @throws PageNotFoundException
     * @throws \Application\Exception\AccessException
     * @throws \Application\Exception\CsrfSecurityException
     */
    public static function run(): Route
    {
        $url = self::getUrl();

        switch ($url) {

            // Home

            case '/':
                $controller = HomeController::class;
                $method = 'showHome';
                $params = [];
                break;

            case '/home':
                $controller = HomeController::class;
                $method = 'showHome';
                $params = [];

                if (isset($_GET['categories'])) {
                    $params = $_GET['categories'];
                }

                if (isset($_GET['action']) && $_GET['action'] === 'contact') {
                    $method = 'contact';
                }
                break;

            // Blog

            case '/blog':
                $controller = BlogController::class;
                $method = 'showPostsOfACategory';
                $params = [
                    'categoryId' => (int) $_GET['category-id'],
                    'page' => (int) $_GET['page']
                ];
                break;

            case '/blog/tag':
                $controller = BlogController::class;
                $method = 'showPostsOfATag';
                $params = [
                    'tagId' => (int) $_GET['tag-id'],
                    'page' => (int) $_GET['page']
                ];
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
                    throw new PageNotFoundException("L'article demandé n'existe pas.");
                }
                break;

            // Comments

            case '/add-comment':
                CsrfProtector::checkCsrf();
                $controller = BlogController::class;
                $method = 'addComment';
                $params = [];

                break;

            // Member

            case '/password-lost':
                $controller = MemberController::class;

                if (isset($_GET['action']) && $_GET['action'] = 'send') {
                    $method = 'sendPasswordRecoveryMail';
                    $params = [
                        'email' => htmlspecialchars($_POST['email'])
                    ];
                } else {
                    $method = 'showPasswordRecovery';
                    $message = 'Un mail contenant la marche à suivre va vous être envoyé en remplissant ce formulaire';
                    $params = ['message' => $message];
                }
                break;

            case '/member-profile':
                $controller = MemberController::class;
                $method = 'showMemberProfile';
                $params = ['memberId' => $_GET['id'] ?? null];
                break;

            case '/profile-editor':
                $controller = MemberController::class;
                $method = 'showMemberProfileEditor';
                $params = [
                    'member' => isset($_GET['id']) ? (int) $_GET['id'] : null,
                    'key' => isset($_GET['key']) ? (int) $_GET['key'] : null
                ];

                if (isset($_GET['action']) && !empty($_GET['action'])) {
                    CsrfProtector::checkCsrf();
                    if ($_GET['action'] === 'update') {
                        $method = 'updateProfile';
                    } elseif ($_GET['action'] === 'delete') {
                        $method = 'deleteMember';
                        $params = ['id' => $_POST['id']];
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
                MemberController::verifyAccess();
                $controller = BlogController::class;
                $method = 'showAdminPanel';
                $params = [];
                break;

            case '/admin/comment-editor':
                MemberController::verifyAccess(['moderator']);
                $controller = BlogController::class;
                $method = 'showCommentEditor';
                $params = ['commentToEditId' => $_GET['id']];
                break;

            case '/admin/edit-comment':
                CsrfProtector::checkCsrf();
                $controller = BlogController::class;
                $method = 'editComment';
                $params = [];
                break;

            case '/admin/delete-comment':
                CsrfProtector::checkCsrf();
                $controller = BlogController::class;
                $method = 'deleteComment';
                $params = [];
                break;

            case '/admin/add-post':
                MemberController::verifyAccess(['author']);
                CsrfProtector::checkCsrf();
                $controller = BlogController::class;
                $method = 'addPost';
                $params = [];
                break;

            case '/admin/edit-post':
                MemberController::verifyAccess(['author', 'editor']);
                CsrfProtector::checkCsrf();
                $controller = BlogController::class;
                $method = 'editPost';
                $params = [];
                break;

            case '/admin/delete-post':
                MemberController::verifyAccess(['author', 'editor']);
                CsrfProtector::checkCsrf();
                $controller = BlogController::class;
                $method = 'deletePost';
                $params = [];
                break;

            case '/admin/post-editor':
                MemberController::verifyAccess(['author', 'editor']);
                if (isset($_POST['post-id'])) {
                    $postId = (int) $_POST['post-id'];
                }
                $controller = BlogController::class;
                $method = 'showPostEditor';
                $params = ['postId' => $postId] ?? [];
                break;

            case '/admin/category-editor':
                MemberController::verifyAccess(['editor']);
                if (isset($_POST['category-id'])) {
                    $categoryId = (int) $_POST['category-id'];
                }
                $controller = BlogController::class;
                $method = 'showCategoryEditor';
                $params = ['categoryId' => $categoryId] ?? [];
                break;

            case '/admin/add-category':
                MemberController::verifyAccess(['editor']);
                CsrfProtector::checkCsrf();
                $controller = BlogController::class;
                $method = 'addCategory';
                $params = [];
                break;

            case '/admin/edit-category':
                MemberController::verifyAccess(['editor']);
                CsrfProtector::checkCsrf();
                $controller = BlogController::class;
                $method = 'editCategory';
                $params = [];
                break;

            case '/admin/delete-category':
                MemberController::verifyAccess(['editor']);
                CsrfProtector::checkCsrf();
                $controller = BlogController::class;
                $method = 'deleteCategory';
                $params = [];
                break;

            case '/admin/update-tags':
                MemberController::verifyAccess(['editor']);
                CsrfProtector::checkCsrf();
                $controller = BlogController::class;
                $method = 'updateTagList';
                $params = [
                    'tagIds' => $_POST['tag_ids'],
                    'tagNames' => $_POST['tag_names'],
                    'action' => $_GET['action'] ?? null
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