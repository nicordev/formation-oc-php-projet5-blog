<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 22/01/2019
 * Time: 14:19
 */

namespace Application;


use Application\Exception\AppException;
use Application\Router\Router;
use ReflectionException;
use ReflectionMethod;

class Application
{
    private $twig;

    const VIEW_404 = 'pageNotFound.twig';

    public function __construct()
    {
        $twigLoader = new Twig_Loader_Filesystem(__DIR__ . '/view');

        $this->twig = new Twig_Environment($twigLoader, [
            'debug' => true, // TODO change to false for production
            'cache' => false // TODO change to true for production
        ]);
    }

    public function run()
    {
        // Routing
        $route = Router::run();

        switch ($route->controller) {
            case 'Controller\BlogController':
                $controller = DIC::newBlogController();
                break;
            default:
                throw new AppException('The DIC does not know the controller ' . $route->controller);
        }
        try {
            $method = new ReflectionMethod($route->controller, $route->method);
        } catch (ReflectionException $e) {
            throw new AppException('The method ' . $route->method . ' was not found in ' . $route->controller);
        }
        $method->invokeArgs($controller, $route->params);
    }

    // Private

    /**
     * Show a page for errors 404
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function showError404()
    {
        echo $this->twig->render(self::VIEW_404);
    }
}