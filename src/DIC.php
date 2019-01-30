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
use Model\Manager\CategoryManager;
use Model\Manager\CommentManager;
use Model\Manager\PostManager;
use Model\Manager\TagManager;
use Twig_Environment;
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
        $twigLoader = new Twig_Loader_Filesystem(__DIR__ . '/view');

        $twig = new Twig_Environment($twigLoader, [
            'debug' => true, // TODO change to false for production
            'cache' => false // TODO change to true for production
        ]);

        return new BlogController(
            new PostManager(),
            new TagManager(),
            new CategoryManager(),
            new CommentManager(),
            $twig
        );
    }

    /**
     * @return HomeController
     */
    public static function newHomeController(): HomeController
    {
        $twigLoader = new Twig_Loader_Filesystem(__DIR__ . '/view');

        $twig = new Twig_Environment($twigLoader, [
            'debug' => true, // TODO change to false for production
            'cache' => false // TODO change to true for production
        ]);

        return new HomeController(
            new PostManager(),
            new CategoryManager(),
            $twig
        );
    }

    /**
     * @return ErrorController
     */
    public static function newErrorController(): ErrorController
    {
        $twigLoader = new Twig_Loader_Filesystem(__DIR__ . '/view');

        $twig = new Twig_Environment($twigLoader, [
            'debug' => true, // TODO change to false for production
            'cache' => false // TODO change to true for production
        ]);

        return new ErrorController($twig);
    }
}