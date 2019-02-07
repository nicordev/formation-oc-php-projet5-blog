<?php

namespace Controller;


use Twig_Environment;

abstract class Controller
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * Controller constructor.
     *
     * @param Twig_Environment $twig
     */
    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Call a view with parameters
     *
     * @param string $view
     * @param array $params
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render(string $view, array $params = [])
    {
        echo $this->twig->render($view, $params);
    }
}