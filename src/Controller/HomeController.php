<?php

namespace Controller;

use Application\MailSender\MailSender;
use Helper\BlogHelper;
use Model\Manager\CategoryManager;
use Model\Manager\MemberManager;
use Model\Manager\PostManager;
use Twig_Environment;

class HomeController extends Controller
{
    protected $postManager;
    protected $categoryManager;
    private $memberManager;

    public const VIEW_HOME = 'home/home.twig';

    public const KEY_CONTACT_NAME = "contact-name";
    public const KEY_CONTACT_EMAIL = "contact-email";
    public const KEY_CONTACT_MESSAGE = "contact-message";
    public const KEY_CATEGORIES = "categories";
    public const KEY_POST_BY_CATEGORY = "postsByCategory";
    public const KEY_NUMBER_OF_POSTS = "numberOfPosts";


    public function __construct(
        PostManager $postManager,
        CategoryManager $categoryManager,
        MemberManager $memberManager,
        Twig_Environment $twig
    ) {
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
                BlogHelper::prepareAPost($post);
            }
        }

        if (
            isset($_POST[self::KEY_CONTACT_NAME]) &&
            isset($_POST[self::KEY_CONTACT_EMAIL]) &&
            isset($_POST[self::KEY_CONTACT_MESSAGE])
        ) {
            if ($this->contact()) {
                $message = 'Votre message a été envoyé.';
            } else {
                $message = 'Votre nom, prénom, email et message doivent être remplis.';
            }
        }

        $this->render(self::VIEW_HOME, [
            self::KEY_CATEGORIES => $categories,
            self::KEY_POST_BY_CATEGORY => $postsByCategory,
            self::KEY_NUMBER_OF_POSTS => $numberOfPostsByCategory,
            BlogController::KEY_MESSAGE => $message
        ]);
    }

    /**
     * Send a message to the admin
     *
     * @return bool
     * @throws \Application\Exception\HttpException
     */
    public function contact()
    {
        if (
            !empty($_POST[self::KEY_CONTACT_NAME]) &&
            !empty($_POST[self::KEY_CONTACT_EMAIL]) &&
            !empty($_POST[self::KEY_CONTACT_MESSAGE])
        ) {
            $admins = $this->memberManager->getMembersByRole('admin');
            $contactName = htmlspecialchars($_POST[self::KEY_CONTACT_NAME]);
            $subject = "Blog de Nicolas Renvoisé : un message de {$contactName} pour l'admin.";
            $message = htmlspecialchars($_POST[self::KEY_CONTACT_MESSAGE]);
            $from = htmlspecialchars($_POST[self::KEY_CONTACT_EMAIL]);

            foreach ($admins as $admin) {
                MailSender::send(
                    $admin->getEmail(),
                    $subject,
                    $message,
                    $from
                );
            }
            return true;
        }
        return false;
    }
}
