<?php

namespace Controller;

use Twig_Environment;

class ErrorController extends Controller
{
    const VIEW_404 = 'error/pageNotFound.twig';
    const VIEW_403 = 'error/accessDenied.twig';
    const VIEW_500 = 'error/serverError.twig';
    const VIEW_CUSTOM = 'error/customError.twig';

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
        $this->render(self::VIEW_404);
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
        $this->render(self::VIEW_403);
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
        $this->render(self::VIEW_500);
    }

    /**
     * Show a page with a custom message
     *
     * @param string $message
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showCustomError(string $message = '<strong>Erreur !</strong>')
    {
        $this->render(self::VIEW_CUSTOM, ['message' => $message]);
    }
}