<?php

namespace Controller;

use Application\Exception\AppException;
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
                if ($post->getLastModificationDate()) {
                    $post->setLastModificationDate(self::formatDate($post->getLastModificationDate()));
                }
            }
        }

        self::render(self::VIEW_HOME, [
            'categories' => $categories,
            'postsByCategory' => $postsByCategory,
            'numberOfPosts' => 5,
            'message' => $message,
            'connectedMember' => isset($_SESSION['connected-member']) ? $_SESSION['connected-member'] : null
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
            !empty($_POST['contact-email']) &&
            !empty($_POST['contact-message'])
        ) {
            $admins = $this->memberManager->getMembersByRole('admin');

            $subject = "Blog de Nicolas Renvoisé : un message pour l'admin.";
            $message = htmlspecialchars($_POST['contact-message']);
            $header = 'From:' . htmlspecialchars($_POST['contact-email']);

            foreach ($admins as $admin) {
                mail($admin->getEmail(), $subject, $message, $header);
            }
            $this->showHome('Votre message a été envoyé.');

        } else {
            $this->showHome('Votre email et votre message doivent être remplis.');
        }
    }
}