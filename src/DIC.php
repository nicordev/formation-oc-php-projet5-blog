<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 28/01/2019
 * Time: 11:51
 */

namespace Application;


use Application\Security\CsrfProtector;
use Controller\AdminController;
use Controller\BlogController;
use Controller\ErrorController;
use Controller\HomeController;
use Controller\MediaController;
use Controller\MemberController;
use Helper\MemberHelper;
use Model\Entity\Member;
use Model\Manager\CategoryManager;
use Model\Manager\CommentManager;
use Model\Manager\KeyManager;
use Model\Manager\MemberManager;
use Model\Manager\PostManager;
use Model\Manager\RoleManager;
use Model\Manager\TagManager;
use Twig_Environment;
use Twig_Function;
use Twig_Loader_Filesystem;

class DIC
{
    /**
     * Disable the constructor to simulate a static class
     */
    private function __construct()
    {}

    /**
     * @return BlogController
     * @throws Exception\HttpException
     */
    public static function newBlogController(): BlogController
    {
        return new BlogController(
            new PostManager(),
            new TagManager(),
            new CategoryManager(),
            new CommentManager(),
            new MemberManager(),
            self::generateTwigEnvironment()
        );
    }

    /**
     * @return AdminController
     * @throws Exception\HttpException
     */
    public static function newAdminController(): AdminController
    {
        return new AdminController(
            new PostManager(),
            new TagManager(),
            new CategoryManager(),
            new CommentManager(),
            new MemberManager(),
            self::generateTwigEnvironment()
        );
    }

    /**
     * @return MediaController
     */
    public static function newMediaController(): MediaController
    {
        return new MediaController(
            self::generateTwigEnvironment()
        );
    }

    /**
     * @return HomeController
     * @throws Exception\HttpException
     */
    public static function newHomeController(): HomeController
    {
        return new HomeController(
            new PostManager(),
            new CategoryManager(),
            new MemberManager(),
            self::generateTwigEnvironment()
        );
    }

    /**
     * @return ErrorController
     */
    public static function newErrorController(): ErrorController
    {
        return new ErrorController(self::generateTwigEnvironment());
    }

    /**
     * @return MemberController
     * @throws Exception\HttpException
     */
    public static function newMemberController(): MemberController
    {
        return new MemberController(
            new MemberManager(),
            new RoleManager(),
            new PostManager(),
            new CommentManager(),
            new KeyManager(),
            self::generateTwigEnvironment()
        );
    }

    /**
     * Generate a Twig_Environment and create useful functions
     *
     * @return Twig_Environment
     */
    private static function generateTwigEnvironment(): Twig_Environment
    {
        $twigLoader = new Twig_Loader_Filesystem(__DIR__ . '/view');

        $twig = new Twig_Environment($twigLoader, [
            'debug' => true, // Change to false for production
            'cache' => false // Change to true for production
        ]);

        // Get the connected member
        $getUserFunction = new Twig_Function('getUser', function () {
            if (MemberHelper::memberConnected()) {
                return $_SESSION['connected-member'];
            }
            return null;
        });
        $twig->addFunction($getUserFunction);

        // Check if the connected member can have access to the admin panel
        $canAccessAdmin = new Twig_Function('canAccessAdmin', function (?Member $user) {
            if (MemberHelper::memberConnected()) {
                if (MemberController::hasAuthorizedRole(MemberController::AUTHORIZED_ROLES, $user->getRoles())) {
                    return true;
                }
            }
            return false;
        });
        $twig->addFunction($canAccessAdmin);

        // Get the counter CSRF token
        $getCsrfToken = new Twig_Function('getCsrfToken', function () {
            return CsrfProtector::getCounterCsrfToken();
        });
        $twig->addFunction($getCsrfToken);

        // Get the current url
        $getCurrentUrl = new Twig_Function('getCurrentUrl', function () {
            return $_SERVER['REQUEST_URI'];
        });
        $twig->addFunction($getCurrentUrl);

        // Get the size of an image
        $getImageSize = new Twig_Function('getImageSize', function (string $imageUrl) {
            if ($imageData = getimagesize(ROOT_PATH . $imageUrl)) {
                return [
                    'width' => $imageData[0],
                    'height' =>$imageData[1]
                ];
            }
            return null;
        });
        $twig->addFunction($getImageSize);

        // Get categories
        $getCategories = new Twig_Function('getCategories', function () {
            $categoryManager = new CategoryManager();
            return $categoryManager->getAll();
        });
        $twig->addFunction($getCategories);

        // Get members
        $getMembers = new Twig_Function('getMembers', function () {
            $memberManager = new MemberManager();
            return $memberManager->getAll();
        });
        $twig->addFunction($getMembers);

        // DEBUG: Show the content of variables
        $dump = new Twig_Function('dump', function () {
            $args = func_get_args();
            var_dump($args);
            die;
        });
        $twig->addFunction($dump);

        return $twig;
    }
}
