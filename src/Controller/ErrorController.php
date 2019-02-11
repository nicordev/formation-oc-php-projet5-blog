<?php

namespace Controller;

use Twig_Environment;

class ErrorController extends Controller
{
    const VIEW_404 = 'pageNotFound.twig';

    public function __construct(Twig_Environment $twig)
    {
        parent::__construct($twig);
    }

    /**
     * Show a page for errors 404
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showError404()
    {
        echo $this->twig->render(self::VIEW_404, ['connectedMember' => isset($_SESSION['connected-member']) ? $_SESSION['connected-member'] : null]);
    }
}