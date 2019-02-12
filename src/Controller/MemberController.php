<?php

namespace Controller;


use Application\Exception\AppException;
use Application\Exception\MemberException;
use Model\Entity\Member;
use Model\Entity\Role;
use Model\Manager\MemberManager;
use Model\Manager\RoleManager;
use Twig_Environment;

class MemberController extends Controller
{
    protected $memberManager;
    protected $roleManager;
    protected $websiteManager; // TODO: implement the manager

    const VIEW_REGISTRATION = 'member/registrationPage.twig';
    const VIEW_CONNECTION = 'member/connectionPage.twig';
    const VIEW_WELCOME = 'member/welcomePage.twig';
    const VIEW_MEMBER_PROFILE = 'member/profilePage.twig';
    const VIEW_MEMBER_PROFILE_EDITOR = 'member/profileEditor.twig';

    public function __construct(
        MemberManager $memberManager,
        RoleManager $roleManager,
        Twig_Environment $twig
    )
    {
        parent::__construct($twig);
        $this->memberManager = $memberManager;
        $this->roleManager = $roleManager;
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
        echo $this->twig->render(self::VIEW_WELCOME, ['connectedMember' => isset($_SESSION['connected-member']) ? $_SESSION['connected-member'] : null]);
    }

    /**
     * Show the static profile of a member
     *
     * @param Member $member
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showMemberProfile(?Member $member = null)
    {
        if ($member === null) {
            $member = $_SESSION['connected-member'];
        }
        echo $this->twig->render(self::VIEW_MEMBER_PROFILE, [
            'member' => $member,
            'connectedMember' => isset($_SESSION['connected-member']) ? $_SESSION['connected-member'] : null
        ]);
    }

    /**
     * Show the profile of a member with the ability to edit it
     *
     * @param Member|null $member
     * @throws AppException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showMemberProfileEditor(?Member $member = null)
    {
        if (isset($_SESSION['connected-member']) && $_SESSION['connected-member'] !== null) {
            echo $this->twig->render(self::VIEW_MEMBER_PROFILE_EDITOR, [
                'member' => isset($member) ? $member : $_SESSION['connected-member'],
                'connectedMember' => isset($_SESSION['connected-member']) ? $_SESSION['connected-member'] : null
            ]);
        } else {
            throw new AppException('You can not edit a profile if you are not connected.');
        }
    }

    // Actions

    /**
     * Update the profile of a member
     *
     * @throws AppException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function updateProfile()
    {
        if (
            isset($_POST['name']) &&
            isset($_POST['email']) &&
            isset($_POST['description'])
        ) {
            $modifiedMember = $this->buildMemberFromForm();
            $this->memberManager->edit($modifiedMember);
            $this->showMemberProfile($modifiedMember);

        } else {
            throw new AppException('$_POST lacks the requested keys to update the member.');
        }
    }

    public function deleteMember(int $memberId)
    {
        $this->memberManager->delete($memberId);
        unset($_SESSION['connected-member']);
        header('Location: /home'); // TODO: make a dedicated page
    }

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
                if ($this->addNewMember($member)) {
                    $_SESSION['connected-member'] = $member;
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

        if (isset($_POST['password'])) {
            $member->setPassword(htmlspecialchars($_POST['password']));
        }

        $member->setName(htmlspecialchars($_POST['name']));

        if (isset($_POST['description'])) {
            $member->setDescription(htmlspecialchars($_POST['description']));
        }

        if (isset($_POST['websites'])) {
            $member->setWebsites($_POST['websites']);
        }

        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $member->setId((int) $_POST['id']);
        }

        return $member;
    }

    /**
     * Add a new Member
     *
     * @param Member $member
     * @return bool
     * @throws \Application\Exception\BlogException
     */
    private function addNewMember(Member $member): bool
    {
        if ($this->memberManager->isNewMember($member)) {
            $member->setPassword(password_hash($member->getPassword(), PASSWORD_DEFAULT));
            $role = new Role([
                'id' => $this->roleManager->getId('member'),
                'name' => 'member'
            ]);
            $member->setRoles([$role]);
            $this->memberManager->add($member);

            return true;
        }

        return false;
    }
}