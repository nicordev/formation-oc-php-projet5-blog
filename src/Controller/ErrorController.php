<?php

namespace Controller;

use Twig_Environment;

class ErrorController extends Controller
{
    const VIEW_404 = 'error/pageNotFound.twig';
    const VIEW_403 = 'error/accessDenied.twig';
    const VIEW_500 = 'error/server';

    public function __construct(Twig_Environment $twig)
    {
        parent::__construct($twig);
    }

    /**
     * Show a page for errors 404 (Not Found)
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showError404()
    {
        echo $this->twig->render(self::VIEW_404);
    }

    /**
     * Show a page for errors 403 (Forbidden)
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showError403()
    {
        echo $this->twig->render(self::VIEW_403);
    }

    /**
     * Show a page for errors 500 (Internal Server Error)
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showError500()
    {
        echo $this->twig->render(self::VIEW_500);
    }


}