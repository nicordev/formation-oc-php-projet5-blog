<?php

namespace Controller;

use Application\MailSender\MailSender;
use Model\Manager\CategoryManager;
use Model\Manager\MemberManager;
use Model\Manager\PostManager;
use Twig_Environment;

class HomeController extends Controller
{
    protected $postManager;
    protected $categoryManager;
    private $memberManager;

    const VIEW_HOME = 'home/home.twig';

    public function __construct(
                                PostManager $postManager,
                                CategoryManager $categoryManager,
                                MemberManager $memberManager,
                                Twig_Environment $twig
    )
    {
        parent::__construct($twig);
        $this->postManager = $postManager;
        $this->categoryManager = $categoryManager;
        $this->memberManager = $memberManager;
    }

    /**
     * Show the home page
     *
     * @param string|null $message
     * @throws \Application\Exception\HttpException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Exception
     */
    public function showHome(?string $message = null)
    {
        $categories = $this->categoryManager->getAll();
        $numberOfPostsByCategory = 6;
        $postsByCategory = [];

        foreach ($categories as $category) {
            $catId = $category->getId();
            $postsByCategory[$catId] = $this->postManager->getPostsOfACategory($category->getId(), $numberOfPostsByCategory + 1, null, false);
            // Format creation dates and translate markdown
            foreach ($postsByCategory[$catId] as $post) {
                BlogController::prepareAPost($post);
            }
        }

        $this->render(self::VIEW_HOME, [
            'categories' => $categories,
            'postsByCategory' => $postsByCategory,
            'numberOfPosts' => $numberOfPostsByCategory,
            'message' => $message
        ]);
    }

    /**
     * Send a message to the admin
     *
     * @throws \Application\Exception\HttpException
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
            $from = htmlspecialchars($_POST['contact-email']);

            foreach ($admins as $admin) {
                MailSender::send(
                    $admin->getEmail(),
                    $subject,
                    $message,
                    $from
                );
            }
            $this->showHome('Votre message a été envoyé.');

        } else {
            $this->showHome('Votre nom, prénom, email et message doivent être remplis.');
        }
    }
}