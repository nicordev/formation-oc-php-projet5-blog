<?php

namespace Controller;


use Application\Exception\AccessException;
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

    public const VIEW_REGISTRATION = 'member/registrationPage.twig';
    public const VIEW_CONNECTION = 'member/connectionPage.twig';
    public const VIEW_WELCOME = 'member/welcomePage.twig';
    public const VIEW_MEMBER_PROFILE = 'member/profilePage.twig';
    public const VIEW_MEMBER_PROFILE_EDITOR = 'member/profileEditor.twig';
    public const AUTHORIZED_ROLES = ['author', 'admin', 'editor', 'moderator'];

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

    /**
     * Check if the connected member can have access to the admin section
     *
     * @return bool
     * @throws AccessException
     */
    public static function verifyAccess(): bool
    {
        if (isset($_SESSION['connected-member'])) {
            foreach ($_SESSION['connected-member']->getRoles() as $role) {
                if (in_array($role, self::AUTHORIZED_ROLES)) {
                    return true;
                }
            }
            throw new AccessException('Access denied. You lack the proper role.');
        }
        throw new AccessException('Access denied. You are not connected.');
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
        echo $this->twig->render(self::VIEW_CONNECTION, [
            'message' => $message,
            'connectedMember' => isset($_SESSION['connected-member']) ? $_SESSION['connected-member'] : null
        ]);
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
     * @param int|null $memberId
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showMemberProfile(?int $memberId = null)
    {
        if ($memberId !== null) {
            $member = $this->memberManager->get($memberId);
        } else {
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
    public function showMemberProfileEditor($member = null)
    {
        if (isset($_SESSION['connected-member']) && $_SESSION['connected-member'] !== null) {

            if (!($member instanceof Member) && in_array('admin', $_SESSION['connected-member']->getRoles())) {
                $member = $this->memberManager->get($member);
            } elseif ($member === null) {
                $member = $_SESSION['connected-member'];
            }

            echo $this->twig->render(self::VIEW_MEMBER_PROFILE_EDITOR, [
                'member' => $member,
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
     * @throws \Application\Exception\BlogException
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
            if ($modifiedMember->getId() === $_SESSION['connected-member']->getId()) {
                $_SESSION['connected-member'] = $modifiedMember;
            }
            $this->showMemberProfile($modifiedMember->getId());

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
     * @throws \Application\Exception\BlogException
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

        if (isset($_POST['roles'])) {
            $roles = ['member'];
            foreach ($_POST['roles'] as $role) {
                if ($this->roleManager->isValid($role)) {
                    $roles[] = $role;
                }
            }
            $member->setRoles($roles);
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