<?php

namespace Application\Router;

use Application\Exception\PageNotFoundException;
use Application\Security\CsrfProtector;
use Controller\AdminController;
use Controller\BlogController;
use Controller\ErrorController;
use Controller\HomeController;
use Controller\MediaController;
use Controller\MemberController;

class Router
{
    public const KEY_NAME = "name";
    public const KEY_URLS = "urls";
    public const KEY_CONTROLLER = "controller";
    public const KEY_METHOD = "method";
    public const KEY_PARAMS = "params";
    public const KEY_CHECK_ACCESS = "checkAccess";
    public const KEY_CHECK_CSRF = "checkCsrf";

    /**
     * Disable the constructor to simulate a static class
     */
    private function __construct()
    {}

    /**
     * Analyze the url and return the controller name, the method to call and the parameters
     *
     * @return Route
     * @throws \Application\Exception\AccessException
     * @throws \Application\Exception\CsrfSecurityException
     */
    public static function run(): Route
    {
        $requestedUrl = self::getUrl();

        return self::getMatchingRoute($requestedUrl);
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

    /**
     * Get the matching route of an url
     *
     * @param string $requestedUrl
     * @return Route|null
     * @throws \Application\Exception\AccessException
     * @throws \Application\Exception\CsrfSecurityException
     */
    private static function getMatchingRoute(string $requestedUrl)
    {
        $routes = [
            // Home
            [
                self::KEY_URLS => [
                    '/',
                    '/home'
                ],
                self::KEY_NAME => "show_home",
                self::KEY_CONTROLLER => HomeController::class,
                self::KEY_METHOD => 'showHome'
            ],
            // Blog
            [
                self::KEY_URLS => [
                    '/blog'
                ],
                self::KEY_NAME => "show_category",
                self::KEY_CONTROLLER => BlogController::class,
                self::KEY_METHOD => 'showPostsOfACategory',
                self::KEY_PARAMS => [
                    'categoryId' => (int) $_GET['category-id'],
                    'page' => (int) $_GET['page']
                ]
            ],
            [
                self::KEY_URLS => [
                    '/blog/tag'
                ],
                self::KEY_NAME => "show_tag_page",
                self::KEY_CONTROLLER => BlogController::class,
                self::KEY_METHOD => 'showPostsOfATag',
                self::KEY_PARAMS => [
                    'tagId' => (int) $_GET['tag-id'],
                    'page' => (int) $_GET['page']
                ]
            ],
            [
                self::KEY_URLS => [
                    '/blog-post'
                ],
                self::KEY_NAME => "show_blog_post",
                self::KEY_CONTROLLER => BlogController::class,
                self::KEY_METHOD => 'showASinglePost',
                self::KEY_PARAMS => [
                    'postId' => (int) $_GET['post-id']
                ]
            ],
            // Comments
            [
                self::KEY_URLS => [
                    '/add-comment'
                ],
                self::KEY_NAME => "add_comment",
                self::KEY_CONTROLLER => AdminController::class,
                self::KEY_METHOD => 'addComment',
                self::KEY_CHECK_ACCESS => ['member'],
                self::KEY_CHECK_CSRF => true
            ],
            // Member
            [
                self::KEY_URLS => [
                    '/password-lost'
                ],
                self::KEY_NAME => "password_recovery",
                self::KEY_CONTROLLER => MemberController::class,
                self::KEY_METHOD => 'passwordLost'
            ],
            [
                self::KEY_URLS => [
                    '/member-profile'
                ],
                self::KEY_NAME => "member_profile",
                self::KEY_CONTROLLER => MemberController::class,
                self::KEY_METHOD => 'showMemberProfile',
                self::KEY_PARAMS => ['memberId' => (int) $_GET['id'] ?? null]
            ],
            [
                self::KEY_URLS => [
                    '/profile-editor'
                ],
                self::KEY_NAME => "profile_editor",
                self::KEY_CONTROLLER => MemberController::class,
                self::KEY_METHOD => 'profileEditor',
                self::KEY_PARAMS => [
                    'member' => isset($_GET['id']) ? (int) $_GET['id'] : null,
                    'key' => isset($_GET['key']) ? (int) $_GET['key'] : null
                ]
            ],
            [
                self::KEY_URLS => [
                    '/registration'
                ],
                self::KEY_NAME => "registration",
                self::KEY_CONTROLLER => MemberController::class,
                self::KEY_METHOD => 'register'
            ],
            [
                self::KEY_URLS => [
                    '/connection'
                ],
                self::KEY_NAME => "connection",
                self::KEY_CONTROLLER => MemberController::class,
                self::KEY_METHOD => 'connect'
            ],
            [
                self::KEY_URLS => [
                    '/disconnection'
                ],
                self::KEY_NAME => "disconnection",
                self::KEY_CONTROLLER => MemberController::class,
                self::KEY_METHOD => 'disconnect'
            ],
            // Admin
            [
                self::KEY_URLS => [
                    '/admin'
                ],
                self::KEY_NAME => "admin_panel",
                self::KEY_CONTROLLER => AdminController::class,
                self::KEY_METHOD => 'showAdminPanel'
            ],
            [
                self::KEY_URLS => [
                    '/admin/comment-editor'
                ],
                self::KEY_NAME => "comment_editor",
                self::KEY_CONTROLLER => AdminController::class,
                self::KEY_METHOD => 'showCommentEditor',
                self::KEY_CHECK_ACCESS => ['moderator'],
                self::KEY_PARAMS => ['commentToEditId' => (int) $_GET['id']]
            ],
            [
                self::KEY_URLS => [
                    '/admin/edit-comment'
                ],
                self::KEY_NAME => "edit_comment",
                self::KEY_CONTROLLER => AdminController::class,
                self::KEY_METHOD => 'editComment',
                self::KEY_CHECK_ACCESS => ['moderator'],
                self::KEY_CHECK_CSRF => true
            ],
            [
                self::KEY_URLS => [
                    '/admin/delete-comment'
                ],
                self::KEY_NAME => "delete_comment",
                self::KEY_CONTROLLER => AdminController::class,
                self::KEY_METHOD => 'deleteComment',
                self::KEY_CHECK_ACCESS => ['moderator'],
                self::KEY_CHECK_CSRF => true
            ],
            [
                self::KEY_URLS => [
                    '/admin/add-post'
                ],
                self::KEY_NAME => "add_post",
                self::KEY_CONTROLLER => AdminController::class,
                self::KEY_METHOD => 'addPost',
                self::KEY_CHECK_ACCESS => ['author'],
                self::KEY_CHECK_CSRF => true
            ],
            [
                self::KEY_URLS => [
                    '/admin/edit-post'
                ],
                self::KEY_NAME => "edit_post",
                self::KEY_CONTROLLER => AdminController::class,
                self::KEY_METHOD => 'editPost',
                self::KEY_CHECK_ACCESS => ['author', 'editor'],
                self::KEY_CHECK_CSRF => true
            ],
            [
                self::KEY_URLS => [
                    '/admin/delete-post'
                ],
                self::KEY_NAME => "delete_post",
                self::KEY_CONTROLLER => AdminController::class,
                self::KEY_METHOD => 'deletePost',
                self::KEY_CHECK_ACCESS => ['author', 'editor'],
                self::KEY_CHECK_CSRF => true
            ],
            [
                self::KEY_URLS => [
                    '/admin/post-editor'
                ],
                self::KEY_NAME => "post_editor",
                self::KEY_CONTROLLER => AdminController::class,
                self::KEY_METHOD => 'showPostEditor',
                self::KEY_CHECK_ACCESS => ['author', 'editor']
            ],
            [
                self::KEY_URLS => [
                    '/admin/category-editor'
                ],
                self::KEY_NAME => "category_editor",
                self::KEY_CONTROLLER => AdminController::class,
                self::KEY_METHOD => 'showCategoryEditor',
                self::KEY_PARAMS => ['categoryId' => (int) $_POST['category-id'] ?? null],
                self::KEY_CHECK_ACCESS => ['editor'],
                self::KEY_CHECK_CSRF => true
            ],
            [
                self::KEY_URLS => [
                    '/admin/edit-category'
                ],
                self::KEY_NAME => "edit_category",
                self::KEY_CONTROLLER => AdminController::class,
                self::KEY_METHOD => 'editCategory',
                self::KEY_CHECK_ACCESS => ['editor'],
                self::KEY_CHECK_CSRF => true
            ],
            [
                self::KEY_URLS => [
                    '/admin/delete-category'
                ],
                self::KEY_NAME => "delete_category",
                self::KEY_CONTROLLER => AdminController::class,
                self::KEY_METHOD => 'deleteCategory',
                self::KEY_CHECK_ACCESS => ['editor'],
                self::KEY_CHECK_CSRF => true
            ],
            [
                self::KEY_URLS => [
                    '/admin/update-tags'
                ],
                self::KEY_NAME => "update_tags",
                self::KEY_CONTROLLER => AdminController::class,
                self::KEY_METHOD => 'updateTagList',
                self::KEY_PARAMS => [
                    'tagIds' => $_POST['tag_ids'],
                    'tagNames' => $_POST['tag_names'],
                    'action' => $_GET['action'] ?? null
                ],
                self::KEY_CHECK_ACCESS => ['editor'],
                self::KEY_CHECK_CSRF => true
            ],
            // Media library
            [
                self::KEY_URLS => [
                    '/admin/media-library'
                ],
                self::KEY_NAME => "media_library",
                self::KEY_CONTROLLER => MediaController::class,
                self::KEY_METHOD => 'showMediaLibrary',
                self::KEY_CHECK_ACCESS => ['author', 'editor'],
                self::KEY_CHECK_CSRF => true
            ],
            [
                self::KEY_URLS => [
                    '/admin/media-library/add'
                ],
                self::KEY_NAME => "add_image",
                self::KEY_CONTROLLER => MediaController::class,
                self::KEY_METHOD => 'addImage',
                self::KEY_CHECK_ACCESS => ['author', 'editor'],
                self::KEY_CHECK_CSRF => true
            ],
            [
                self::KEY_URLS => [
                    '/admin/image-editor'
                ],
                self::KEY_NAME => "image_editor",
                self::KEY_CONTROLLER => MediaController::class,
                self::KEY_METHOD => 'showImageEditor',
                self::KEY_PARAMS => [
                    'imagePath' => htmlspecialchars($_GET['image'])
                ],
                self::KEY_CHECK_ACCESS => ['author', 'editor'],
                self::KEY_CHECK_CSRF => true
            ],
            [
                self::KEY_URLS => [
                    '/admin/image-editor/edit'
                ],
                self::KEY_NAME => "edit_image",
                self::KEY_CONTROLLER => MediaController::class,
                self::KEY_METHOD => 'editImage',
                self::KEY_PARAMS => [
                    'imagePath' => htmlspecialchars($_POST['path']),
                    'cropParameters' => [
                        'width' => (int) $_POST['crop-width'] ?? null,
                        'height' => (int) $_POST['crop-height'] ?? null,
                        'x' => (int) $_POST['crop-x'] ?? null,
                        'y' => (int) $_POST['crop-y'] ?? null
                    ],
                    'newHeight' => (int) $_POST['resize-height'] ?? null,
                    'newWidth' => (int) $_POST['resize-width'] ?? null
                ],
                self::KEY_CHECK_ACCESS => ['author', 'editor'],
                self::KEY_CHECK_CSRF => true
            ],
            [
                self::KEY_URLS => [
                    '/admin/image-editor/delete'
                ],
                self::KEY_NAME => "delete_image",
                self::KEY_CONTROLLER => MediaController::class,
                self::KEY_METHOD => 'deleteImage',
                self::KEY_PARAMS => [
                    'imagePath' => htmlspecialchars($_POST['path'])
                ],
                self::KEY_CHECK_ACCESS => ['author', 'editor'],
                self::KEY_CHECK_CSRF => true
            ]
        ];

        foreach ($routes as $route) {
            if (self::isAKnownUrl($route['urls'], $requestedUrl)) {
                foreach ($route as $key => $value) {
                    if ($key === "checkAccess") {
                        MemberController::verifyAccess($value);
                    }
                    if ($key === "checkCsrf") {
                        CsrfProtector::checkCsrf();
                    }
                }
                return new Route($route['controller'], $route['method'], $route['params'] ?? []);
            }
        }
        return null;
    }

    /**
     * Check if an url exists in the router
     *
     * @param array $knownUrls
     * @param string $requestedUrl
     * @return bool
     */
    private static function isAKnownUrl(array $knownUrls, string $requestedUrl)
    {
        foreach ($knownUrls as $url) {
            if ($url === $requestedUrl) {
                return true;
            }
        }
        return false;
    }
}