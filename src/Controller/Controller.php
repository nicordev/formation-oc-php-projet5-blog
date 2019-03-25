<?php

namespace Controller;


use DateTime;
use Twig_Environment;

abstract class Controller
{
    const MYSQL_DATE_FORMAT = "Y-m-d H:i:s";
    const WEBSITE_DATE_FORMAT = "d/m/Y à H:i";

    const KEY_ACTION = "action";

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

    /**
     * Format a date to match the website requirement
     *
     * @param string $date
     * @return string
     * @throws \Exception
     */
    public static function formatDate(string $date)
    {
        $date = new DateTime($date);
        return $date->format(self::WEBSITE_DATE_FORMAT);
    }
}
