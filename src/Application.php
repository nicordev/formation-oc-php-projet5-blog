<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 22/01/2019
 * Time: 14:19
 */

namespace Application;


use Application\Exception\AccessException;
use Application\Exception\AppException;
use Application\Exception\HttpException;
use Application\Exception\PageNotFoundException;
use Application\Router\Router;
use Controller\BlogController;
use Controller\ErrorController;
use Controller\HomeController;
use Controller\MemberController;
use ReflectionException;
use ReflectionMethod;

class Application
{
    /**
     * Begin the show! Enjoy!
     *
     * @throws AppException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function run()
    {
        // Session
        session_start();

        // Time zone
        date_default_timezone_set("Europe/Paris");

        // Routing
        try {
            $route = Router::run();

            switch ($route->controller) {
                case BlogController::class:
                    $controller = DIC::newBlogController();
                    break;

                case HomeController::class:
                    $controller = DIC::newHomeController();
                    break;

                case ErrorController::class:
                    $controller = DIC::newErrorController();
                    break;

                case MemberController::class:
                    $controller = DIC::newMemberController();
                    break;

                default:
                    throw new AppException('The DIC does not know the controller ' . $route->controller);
            }

            try {
                $method = new ReflectionMethod($route->controller, $route->method);

            } catch (ReflectionException $e) {
                throw new HttpException('The method ' . $route->method . ' was not found in ' . $route->controller, 404, $e);
            }

            $method->invokeArgs($controller, $route->params);

        } catch (AccessException $e) {
            $errorController = DIC::newErrorController();
            $errorController->showError403();
        } catch (PageNotFoundException $e) {
            $errorController = DIC::newErrorController();
            $errorController->showError404();
        } catch (HttpException $e) {
            $errorController = DIC::newErrorController();
            switch ($e->getCode()) {
                case 404:
                    $errorController->showError404();
                    break;
                case 500:
                    $errorController->showError500();
                    break;
            }
        }
    }
}