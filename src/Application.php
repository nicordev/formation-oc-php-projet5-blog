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
use Controller\BlogController;
use ReflectionException;
use ReflectionMethod;

class Application
{
    public function run()
    {
        // Routing
        $route = Router::run();

        switch ($route->controller) {
            case BlogController::class:
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
}