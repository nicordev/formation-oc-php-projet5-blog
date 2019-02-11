<?php

namespace Controller;


use Application\Exception\MemberException;
use Model\Entity\Member;
use Model\Manager\MemberManager;
use Twig_Environment;

class MemberController extends Controller
{
    protected $memberManager;

    const VIEW_REGISTRATION = 'member/registrationPage.twig';
    const VIEW_CONNECTION = 'member/connectionPage.twig';
    const VIEW_WELCOME = 'member/welcomePage.twig';

    public function __construct(
        MemberManager $memberManager,
        Twig_Environment $twig
    )
    {
        parent::__construct($twig);
        $this->memberManager = $memberManager;
    }

    // Views

    /**
     * Show the registration page
     *
     * @param string|null $message
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showRegistrationPage(?string $message = null)
    {
        echo $this->twig->render(self::VIEW_REGISTRATION, ['message' => $message]);
    }

    /**
     * Show the connection page
     *
     * @param string|null $message
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showConnectionPage(?string $message = null)
    {
        echo $this->twig->render(self::VIEW_CONNECTION, ['message' => $message]);
    }

    /**
     * Show a welcome page for new members
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showWelcomePage()
    {
        echo $this->twig->render(self::VIEW_WELCOME);
    }

    // Actions

    /**
     * Register a new member
     *
     * @throws MemberException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Application\Exception\BlogException
     */
    public function register()
    {
        if (
            isset($_POST['name']) &&
            isset($_POST['email']) &&
            isset($_POST['password'])
        ) {
            if (
                empty($_POST['email']) ||
                empty($_POST['password']) ||
                empty($_POST['name'])
            ) {
                $this->showRegistrationPage("Le nom, l'email et le mot de passe doivent être renseignés.");
            } else {
                $member = $this->buildMemberFromForm();
                if ($this->memberManager->isNewMember($member)) {
                    $member->setPassword(password_hash($member->getPassword(), PASSWORD_DEFAULT));
                    $this->memberManager->add($member);
                    $this->showWelcomePage();
                } else {
                    $this->showRegistrationPage("Cet email est déjà pris.");
                }
            }
        } else {
            throw new MemberException('$_POST does not contain requested fields.');
        }
    }

    /**
     * Connect a member from the connection page
     *
     * @throws MemberException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Application\Exception\BlogException
     */
    public function connect()
    {
        if (isset($_POST['email']) && isset($_POST['password'])) {
            if (empty($_POST['email']) || empty($_POST['password'])) {
                $this->showConnectionPage("L'email et le mot de passe doivent être renseignés.");
            } else {
                $member = $this->memberManager->getFromEmail($_POST['email']);
                if ($member !== null) {
                    if (password_verify($_POST['password'], $member->getPassword())) {
                        $_SESSION['connected-member'] = $member;
                        header('Location: /home');
                    }
                }
                $this->showConnectionPage("Erreur dans l'email ou le mot de passe.");
            }
        } else {
            throw new MemberException('$_POST does not contain requested fields.');
        }
    }

    /**
     * Disconnect the member
     */
    public function disconnect()
    {
        unset($_SESSION['connected-member']);
        header('Location: /home');
    }

    // Private

    /**
     * Create a Member from a form with $_POST
     *
     * @return Member
     */
    private function buildMemberFromForm(): Member
    {
        $member = new Member();

        $member->setEmail(htmlspecialchars($_POST['email']));
        $member->setPassword(htmlspecialchars($_POST['password']));
        $member->setName(htmlspecialchars($_POST['name']));

        if (isset($_POST['description']) && !empty($_POST['description'])) {
            $member->setDescription(htmlspecialchars($_POST['description']));
        }

        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $member->setId((int) $_POST['id']);
        }

        return $member;
    }
}