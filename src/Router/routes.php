<?php

use Application\FileHandler\ImageHandler;
use Controller\AdminController;
use Controller\BlogController;
use Controller\HomeController;
use Controller\MediaController;
use Controller\MemberController;
use Model\Entity\Member;

define('ROUTE_KEY_URLS', "urls");
define('ROUTE_KEY_NAME', "name");
define('ROUTE_KEY_CONTROLLER', "controller");
define('ROUTE_KEY_METHOD', "method");
define('ROUTE_KEY_PARAMS', "params");
define('ROUTE_KEY_CHECK_ACCESS', "checkAccess");
define('ROUTE_KEY_CHECK_CSRF', "checkCsrf");


return [

    // Home

    [
        ROUTE_KEY_URLS => [
            '/',
            '/home'
        ],
        ROUTE_KEY_NAME => "show_home",
        ROUTE_KEY_CONTROLLER => HomeController::class,
        ROUTE_KEY_METHOD => 'showHome'
    ],

    // Blog

    [
        ROUTE_KEY_URLS => [
            '/blog'
        ],
        ROUTE_KEY_NAME => "show_category",
        ROUTE_KEY_CONTROLLER => BlogController::class,
        ROUTE_KEY_METHOD => 'showPostsOfACategory',
        ROUTE_KEY_PARAMS => [
            'categoryId' => (int) $_GET['category-id'],
            'page' => (int) $_GET['page']
        ]
    ],
    [
        ROUTE_KEY_URLS => [
            '/blog/tag'
        ],
        ROUTE_KEY_NAME => "show_tag_page",
        ROUTE_KEY_CONTROLLER => BlogController::class,
        ROUTE_KEY_METHOD => 'showPostsOfATag',
        ROUTE_KEY_PARAMS => [
            'tagId' => (int) $_GET['tag-id'],
            'page' => (int) $_GET['page']
        ]
    ],
    [
        ROUTE_KEY_URLS => [
            '/blog-post'
        ],
        ROUTE_KEY_NAME => "show_blog_post",
        ROUTE_KEY_CONTROLLER => BlogController::class,
        ROUTE_KEY_METHOD => 'showASinglePost',
        ROUTE_KEY_PARAMS => [
            'postId' => (int) $_GET['post-id'],
            'message' => null,
            'commentPage' => (int) $_GET['comment-page'] ?? 1
        ]
    ],

    // Comments

    [
        ROUTE_KEY_URLS => [
            '/add-comment'
        ],
        ROUTE_KEY_NAME => "add_comment",
        ROUTE_KEY_CONTROLLER => AdminController::class,
        ROUTE_KEY_METHOD => 'addComment',
        ROUTE_KEY_CHECK_ACCESS => [Member::MEMBER],
        ROUTE_KEY_CHECK_CSRF => true
    ],

    // Member

    [
        ROUTE_KEY_URLS => [
            '/password-lost'
        ],
        ROUTE_KEY_NAME => "password_recovery",
        ROUTE_KEY_CONTROLLER => MemberController::class,
        ROUTE_KEY_METHOD => 'passwordLost'
    ],
    [
        ROUTE_KEY_URLS => [
            '/member-profile'
        ],
        ROUTE_KEY_NAME => "member_profile",
        ROUTE_KEY_CONTROLLER => MemberController::class,
        ROUTE_KEY_METHOD => 'showMemberProfile',
        ROUTE_KEY_PARAMS => ['memberId' => (int) $_GET['id'] ?? null]
    ],
    [
        ROUTE_KEY_URLS => [
            '/profile-editor'
        ],
        ROUTE_KEY_NAME => "profile_editor",
        ROUTE_KEY_CONTROLLER => MemberController::class,
        ROUTE_KEY_METHOD => 'profileEditor',
        ROUTE_KEY_PARAMS => [
            Member::MEMBER => (int) $_GET['id'] ?? null,
            'key' => (int) $_GET['key'] ?? null
        ]
    ],
    [
        ROUTE_KEY_URLS => [
            '/registration'
        ],
        ROUTE_KEY_NAME => "registration",
        ROUTE_KEY_CONTROLLER => MemberController::class,
        ROUTE_KEY_METHOD => 'register'
    ],
    [
        ROUTE_KEY_URLS => [
            '/connection'
        ],
        ROUTE_KEY_NAME => "connection",
        ROUTE_KEY_CONTROLLER => MemberController::class,
        ROUTE_KEY_METHOD => 'connect'
    ],
    [
        ROUTE_KEY_URLS => [
            '/disconnection'
        ],
        ROUTE_KEY_NAME => "disconnection",
        ROUTE_KEY_CONTROLLER => MemberController::class,
        ROUTE_KEY_METHOD => 'disconnect'
    ],
    [
        ROUTE_KEY_URLS => [
            '/ask/author'
        ],
        ROUTE_KEY_NAME => "ask_to_be_author",
        ROUTE_KEY_CONTROLLER => MemberController::class,
        ROUTE_KEY_METHOD => 'askRole',
        ROUTE_KEY_PARAMS => [
            "role" => Member::AUTHOR
        ]
    ],

    // Admin

    [
        ROUTE_KEY_URLS => [
            '/admin'
        ],
        ROUTE_KEY_NAME => "admin_panel",
        ROUTE_KEY_CONTROLLER => AdminController::class,
        ROUTE_KEY_METHOD => 'showAdminPanel'
    ],

    // Comment editor

    [
        ROUTE_KEY_URLS => [
            '/admin/comment-editor'
        ],
        ROUTE_KEY_NAME => "comment_editor",
        ROUTE_KEY_CONTROLLER => AdminController::class,
        ROUTE_KEY_METHOD => 'showCommentEditor',
        ROUTE_KEY_CHECK_ACCESS => [Member::MODERATOR],
        ROUTE_KEY_PARAMS => ['commentToEditId' => (int) $_GET['id']]
    ],
    [
        ROUTE_KEY_URLS => [
            '/admin/edit-comment'
        ],
        ROUTE_KEY_NAME => "edit_comment",
        ROUTE_KEY_CONTROLLER => AdminController::class,
        ROUTE_KEY_METHOD => 'editComment',
        ROUTE_KEY_CHECK_ACCESS => [Member::MODERATOR],
        ROUTE_KEY_CHECK_CSRF => true
    ],
    [
        ROUTE_KEY_URLS => [
            '/admin/delete-comment'
        ],
        ROUTE_KEY_NAME => "delete_comment",
        ROUTE_KEY_CONTROLLER => AdminController::class,
        ROUTE_KEY_METHOD => 'deleteComment',
        ROUTE_KEY_CHECK_ACCESS => [Member::MODERATOR],
        ROUTE_KEY_CHECK_CSRF => true
    ],

    // Post editor

    [
        ROUTE_KEY_URLS => [
            '/admin/post-editor'
        ],
        ROUTE_KEY_NAME => "post_editor",
        ROUTE_KEY_CONTROLLER => AdminController::class,
        ROUTE_KEY_METHOD => 'showPostEditor',
        ROUTE_KEY_CHECK_ACCESS => [Member::AUTHOR, Member::EDITOR]
    ],
    [
        ROUTE_KEY_URLS => [
            '/admin/add-post'
        ],
        ROUTE_KEY_NAME => "add_post",
        ROUTE_KEY_CONTROLLER => AdminController::class,
        ROUTE_KEY_METHOD => 'addPost',
        ROUTE_KEY_CHECK_ACCESS => [Member::AUTHOR],
        ROUTE_KEY_CHECK_CSRF => true
    ],
    [
        ROUTE_KEY_URLS => [
            '/admin/edit-post'
        ],
        ROUTE_KEY_NAME => "edit_post",
        ROUTE_KEY_CONTROLLER => AdminController::class,
        ROUTE_KEY_METHOD => 'editPost',
        ROUTE_KEY_CHECK_ACCESS => [Member::AUTHOR, Member::EDITOR],
        ROUTE_KEY_CHECK_CSRF => true
    ],
    [
        ROUTE_KEY_URLS => [
            '/admin/delete-post'
        ],
        ROUTE_KEY_NAME => "delete_post",
        ROUTE_KEY_CONTROLLER => AdminController::class,
        ROUTE_KEY_METHOD => 'deletePost',
        ROUTE_KEY_CHECK_ACCESS => [Member::AUTHOR, Member::EDITOR],
        ROUTE_KEY_CHECK_CSRF => true
    ],

    // Category editor

    [
        ROUTE_KEY_URLS => [
            '/admin/category-editor'
        ],
        ROUTE_KEY_NAME => "category_editor",
        ROUTE_KEY_CONTROLLER => AdminController::class,
        ROUTE_KEY_METHOD => 'showCategoryEditor',
        ROUTE_KEY_PARAMS => ['categoryId' => (int) $_POST['category-id'] ?? null],
        ROUTE_KEY_CHECK_ACCESS => [Member::EDITOR],
        ROUTE_KEY_CHECK_CSRF => true
    ],
    [
        ROUTE_KEY_URLS => [
            '/admin/edit-category'
        ],
        ROUTE_KEY_NAME => "edit_category",
        ROUTE_KEY_CONTROLLER => AdminController::class,
        ROUTE_KEY_METHOD => 'editCategory',
        ROUTE_KEY_CHECK_ACCESS => [Member::EDITOR],
        ROUTE_KEY_CHECK_CSRF => true
    ],
    [
        ROUTE_KEY_URLS => [
            '/admin/delete-category'
        ],
        ROUTE_KEY_NAME => "delete_category",
        ROUTE_KEY_CONTROLLER => AdminController::class,
        ROUTE_KEY_METHOD => 'deleteCategory',
        ROUTE_KEY_CHECK_ACCESS => [Member::EDITOR],
        ROUTE_KEY_CHECK_CSRF => true
    ],

    // Tags list

    [
        ROUTE_KEY_URLS => [
            '/admin/update-tags'
        ],
        ROUTE_KEY_NAME => "update_tags",
        ROUTE_KEY_CONTROLLER => AdminController::class,
        ROUTE_KEY_METHOD => 'updateTagList',
        ROUTE_KEY_PARAMS => [
            'tagIds' => $_POST['tag_ids'],
            'tagNames' => $_POST['tag_names'],
            'action' => $_GET['action'] ?? null
        ],
        ROUTE_KEY_CHECK_ACCESS => [Member::EDITOR],
        ROUTE_KEY_CHECK_CSRF => true
    ],

    // Media library

    [
        ROUTE_KEY_URLS => [
            '/admin/media-library'
        ],
        ROUTE_KEY_NAME => "media_library",
        ROUTE_KEY_CONTROLLER => MediaController::class,
        ROUTE_KEY_METHOD => 'showMediaLibrary',
        ROUTE_KEY_CHECK_ACCESS => [Member::AUTHOR, Member::EDITOR],
        ROUTE_KEY_CHECK_CSRF => true
    ],
    [
        ROUTE_KEY_URLS => [
            '/admin/media-library/add'
        ],
        ROUTE_KEY_NAME => "add_image",
        ROUTE_KEY_CONTROLLER => MediaController::class,
        ROUTE_KEY_METHOD => 'addImage',
        ROUTE_KEY_CHECK_ACCESS => [Member::AUTHOR, Member::EDITOR],
        ROUTE_KEY_CHECK_CSRF => true
    ],
    [
        ROUTE_KEY_URLS => [
            '/admin/image-editor'
        ],
        ROUTE_KEY_NAME => "image_editor",
        ROUTE_KEY_CONTROLLER => MediaController::class,
        ROUTE_KEY_METHOD => 'showImageEditor',
        ROUTE_KEY_PARAMS => [
            ImageHandler::KEY_IMAGE_PATH => htmlspecialchars($_GET['image'])
        ],
        ROUTE_KEY_CHECK_ACCESS => [Member::AUTHOR, Member::EDITOR],
        ROUTE_KEY_CHECK_CSRF => true
    ],
    [
        ROUTE_KEY_URLS => [
            '/admin/image-editor/edit'
        ],
        ROUTE_KEY_NAME => "edit_image",
        ROUTE_KEY_CONTROLLER => MediaController::class,
        ROUTE_KEY_METHOD => 'editImage',
        ROUTE_KEY_PARAMS => [
            ImageHandler::KEY_IMAGE_PATH => htmlspecialchars($_POST['path']),
            'cropParameters' => [
                'width' => (int) $_POST['crop-width'] ?? null,
                'height' => (int) $_POST['crop-height'] ?? null,
                'x' => (int) $_POST['crop-x'] ?? null,
                'y' => (int) $_POST['crop-y'] ?? null
            ],
            'newHeight' => (int) $_POST['resize-height'] ?? null,
            'newWidth' => (int) $_POST['resize-width'] ?? null
        ],
        ROUTE_KEY_CHECK_ACCESS => [Member::AUTHOR, Member::EDITOR],
        ROUTE_KEY_CHECK_CSRF => true
    ],
    [
        ROUTE_KEY_URLS => [
            '/admin/image-editor/delete'
        ],
        ROUTE_KEY_NAME => "delete_image",
        ROUTE_KEY_CONTROLLER => MediaController::class,
        ROUTE_KEY_METHOD => 'deleteImage',
        ROUTE_KEY_PARAMS => [
            ImageHandler::KEY_IMAGE_PATH => htmlspecialchars($_POST['path'])
        ],
        ROUTE_KEY_CHECK_ACCESS => [Member::AUTHOR, Member::EDITOR],
        ROUTE_KEY_CHECK_CSRF => true
    ]
];
