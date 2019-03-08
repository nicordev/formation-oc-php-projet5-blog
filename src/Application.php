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
use Application\Exception\PageNotFoundException;
use Application\Exception\CsrfSecurityException;
use Application\Router\Router;
use Application\Security\CsrfProtector;
use Controller\BlogController;
use Controller\ErrorController;
use Controller\HomeController;
use Controller\MemberController;
use Exception;
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

        // Security
        try {
            // CSRF protection
            CsrfProtector::setCounterCsrfToken(bin2hex(random_bytes(87)));
            $_SESSION['csrf-token'] = CsrfProtector::getCounterCsrfToken();
        } catch (Exception $e) {
            $errorController = DIC::newErrorController();
            $errorController->showError500();
        }

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
                throw new AppException('The method ' . $route->method . ' was not found in ' . $route->controller, 0, $e);
            }

            $method->invokeArgs($controller, $route->params);

        } catch (AccessException $e) {
            $errorController = DIC::newErrorController();
            $errorController->showError403();
        } catch (PageNotFoundException $e) {
            $errorController = DIC::newErrorController();
            $errorController->showError404();
        } catch (CsrfSecurityException $e) {
            $errorController = DIC::newErrorController();
            $errorController->showCustomError('Une attaque CSRF a été détectée. Si vous êtes à l\'origine de cette attaque, c\'est pas gentil.');
        }
    }
}