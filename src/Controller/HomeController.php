<?php

namespace Controller;

use Model\Manager\CategoryManager;
use Model\Manager\PostManager;
use Twig_Environment;

class HomeController extends Controller
{
    protected $postManager;
    protected $categoryManager;

    const VIEW_HOME = 'home/home.twig';

    public function __construct(
                                PostManager $postManager,
                                CategoryManager $categoryManager,
                                Twig_Environment $twig
    )
    {
        parent::__construct($twig);
        $this->postManager = $postManager;
        $this->categoryManager = $categoryManager;
    }

    /**
     * Show the home page
     *
     * @param string|null $message
     * @throws \Application\Exception\BlogException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showHome(?string $message = null)
    {
        $categories = $this->categoryManager->getAll();
        $postsByCategory = [];

        foreach ($categories as $category) {
            $postsByCategory[$category->getId()] = $this->postManager->getPostsOfACategory($category->getId());
            // Format creation dates
            foreach ($postsByCategory[$category->getId()] as $post) {
                $post->setCreationDate(self::formatDate($post->getCreationDate()));
                BlogController::decodePostExcerpt($post);
                if ($post->getLastModificationDate()) {
                    $post->setLastModificationDate(self::formatDate($post->getLastModificationDate()));
                }
            }
        }

        $this->render(self::VIEW_HOME, [
            'categories' => $categories,
            'postsByCategory' => $postsByCategory,
            'message' => $message
        ]);
    }

    /**
     * Send a message to the admin
     *
     * @throws \Application\Exception\BlogException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function contact()
    {
        if (
            !empty($_POST['contact-name']) &&
            !empty($_POST['contact-email']) &&
            !empty($_POST['contact-message'])
        ) {
            $admins = $this->memberManager->getMembersByRole('admin');
            $contactName = htmlspecialchars($_POST['contact-name']);
            $subject = "Blog de Nicolas Renvoisé : un message de {$contactName} pour l'admin.";
            $message = htmlspecialchars($_POST['contact-message']);
            $header = 'From:' . htmlspecialchars($_POST['contact-email']);

            foreach ($admins as $admin) {
                mail($admin->getEmail(), $subject, $message, $header);
            }
            $this->showHome('Votre message a été envoyé.');

        } else {
            $this->showHome('Votre nom, prénom, email et message doivent être remplis.');
        }
    }
}