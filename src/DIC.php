<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 28/01/2019
 * Time: 11:51
 */

namespace Application;


use Controller\BlogController;
use Controller\ErrorController;
use Controller\HomeController;
use Controller\MemberController;
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
     * @return HomeController
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
            'debug' => true, // TODO change to false for production
            'cache' => false // TODO change to true for production
        ]);

        $getUserFunction = new Twig_Function('getUser', function () {
            if (MemberController::memberConnected()) {
                return $_SESSION['connected-member'];
            }
            return null;
        });

        $twig->addFunction($getUserFunction);

        return $twig;
    }
}