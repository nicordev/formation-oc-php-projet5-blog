<?php

namespace Controller;


use Application\Exception\AccessException;
use Application\Exception\AppException;
use Application\Exception\HttpException;
use Application\Exception\MemberException;
use Application\Exception\CsrfSecurityException;
use Application\MailSender\MailSender;
use Application\Security\BruteForceProtector;
use Application\Security\CsrfProtector;
use Exception;
use Helper\BlogHelper;
use Model\Entity\Key;
use Model\Entity\Member;
use Model\Entity\Role;
use Model\Manager\CommentManager;
use Model\Manager\KeyManager;
use Model\Manager\MemberManager;
use Model\Manager\PostManager;
use Model\Manager\RoleManager;
use Twig_Environment;

class MemberController extends Controller
{
    protected $memberManager;
    protected $roleManager;
    protected $postManager;
    protected $commentManager;
    protected $keyManager;

    public const VIEW_REGISTRATION = 'member/registrationPage.twig';
    public const VIEW_CONNECTION = 'member/connectionPage.twig';
    public const VIEW_WELCOME = 'member/welcomePage.twig';
    public const VIEW_MEMBER_PROFILE = 'member/profilePage.twig';
    public const VIEW_MEMBER_PROFILE_EDITOR = 'member/profileEditor.twig';
    public const VIEW_PASSWORD_RECOVERY = 'member/passwordRecovery.twig';
    public const VIEW_QUIT_PAGE = 'member/quitPage.twig';

    public const AUTHORIZED_ROLES = ['author', 'admin', 'editor', 'moderator'];

    const KEY_CONNECTED_MEMBER = "connected-member";
    const KEY_MEMBER = "member";

    public function __construct(
        MemberManager $memberManager,
        RoleManager $roleManager,
        PostManager $postManager,
        CommentManager $commentManager,
        KeyManager $keyManager,
        Twig_Environment $twig
    )
    {
        parent::__construct($twig);
        $this->memberManager = $memberManager;
        $this->roleManager = $roleManager;
        $this->postManager = $postManager;
        $this->commentManager = $commentManager;
        $this->keyManager = $keyManager;
    }

    /**
     * Check if the connected member can have access to the admin section
     *
     * @param array|null $authorizedRoles
     * @return bool
     * @throws AccessException
     */
    public static function verifyAccess(?array $authorizedRoles = null): bool
    {
        if (MemberController::memberConnected()) {
            if (self::hasAuthorizedRole($authorizedRoles ?? self::AUTHORIZED_ROLES, $_SESSION[self::KEY_CONNECTED_MEMBER]->getRoles())) {
                return true;
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
        $this->render(self::VIEW_REGISTRATION, [BlogController::KEY_MESSAGE => $message]);
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
        $this->render(self::VIEW_WELCOME);
    }

    /**
     * Show the static profile of a member
     *
     * @param int|null $memberId
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws HttpException
     * @throws Exception
     */
    public function showMemberProfile(?int $memberId = null)
    {
        if ($memberId !== null && $memberId > 0) {
            $member = $this->memberManager->get($memberId);
        } else {
            $member = $_SESSION[self::KEY_CONNECTED_MEMBER];
        }

        $memberPosts = $this->postManager->getPostsOfAMember($member->getId(), false, true);
        $memberComments = $this->commentManager->getCommentsOfAMember($member->getId(), true);

        foreach ($memberComments as $memberComment) {
            BlogHelper::convertDatesOfComment($memberComment);
        }

        $this->render(self::VIEW_MEMBER_PROFILE, [
            self::KEY_MEMBER => $member,
            'memberPosts' => $memberPosts,
            'memberComments' => $memberComments
        ]);
    }

    /**
     * Show the profile editor and launch actions like edit or delete
     *
     * @param null $member
     * @param int|null $keyValue
     * @throws AppException
     * @throws CsrfSecurityException
     * @throws HttpException
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function profileEditor($member = null, ?int $keyValue = null)
    {
        CsrfProtector::checkCsrf();
        if (isset($_GET[self::KEY_ACTION]) && !empty($_GET[self::KEY_ACTION])) {
            if ($_GET[self::KEY_ACTION] === 'update') {
                $this->updateProfile();
            } elseif ($_GET[self::KEY_ACTION] === 'delete') {
                $this->deleteMember((int) $_POST['id']);
            }
        } else {
            $this->showMemberProfileEditor($member, $keyValue);
        }
    }

    /**
     * Check if the user is connected
     *
     * @return bool
     */
    public static function memberConnected(): bool
    {
        if (isset($_SESSION[self::KEY_CONNECTED_MEMBER]) && !empty($_SESSION[self::KEY_CONNECTED_MEMBER])) {
            return true;
        }
        return false;
    }

    /**
     * Show a page to recover a lost password
     *
     * @param string|null $message
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showPasswordRecovery(?string $message = null)
    {
        $this->render(self::VIEW_PASSWORD_RECOVERY, [
            BlogController::KEY_MESSAGE => $message
        ]);
    }

    /**
     * Page shown when a member delete his account
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showQuitPage()
    {
        $this->render(self::VIEW_QUIT_PAGE);
    }

    // Actions

    /**
     * Update the profile of a member
     *
     * @throws AppException
     * @throws HttpException
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function updateProfile()
    {
        if (
            isset($_POST['name']) &&
            isset($_POST[Member::KEY_EMAIL]) &&
            isset($_POST[Member::KEY_DESCRIPTION])
        ) {
            $modifiedMember = $this->buildMemberFromForm();
            if (isset($_POST['keep-roles'])) {
                $this->memberManager->edit($modifiedMember, false);
            } else {
                $this->memberManager->edit($modifiedMember, true);
            }
            if ($modifiedMember->getId() === $_SESSION[self::KEY_CONNECTED_MEMBER]->getId()) {
                $_SESSION[self::KEY_CONNECTED_MEMBER] = $modifiedMember;
            }
            $this->showMemberProfile($modifiedMember->getId());

        } else {
            throw new AppException('$_POST lacks the requested keys to update the member.');
        }
    }

    /**
     * Delete a member
     *
     * @param int $memberId
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws HttpException
     */
    public function deleteMember(int $memberId)
    {
        $this->memberManager->delete($memberId);
        if ($_SESSION[self::KEY_CONNECTED_MEMBER]->getId() === $memberId) {
            unset($_SESSION[self::KEY_CONNECTED_MEMBER]);
            $this->render(self::VIEW_QUIT_PAGE);
        } else {
            header('Location: /admin#admin-member-list');
        }
    }

    /**
     * Register a new member
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Application\Exception\HttpException
     */
    public function register()
    {
        if (
            isset($_POST['name']) &&
            isset($_POST[Member::KEY_EMAIL]) &&
            isset($_POST[Member::KEY_PASSWORD])
        ) {
            if (
                empty($_POST[Member::KEY_EMAIL]) ||
                empty($_POST[Member::KEY_PASSWORD]) ||
                empty($_POST['name'])
            ) {
                $this->showRegistrationPage("Le nom, l'email et le mot de passe doivent être renseignés.");
            } else {
                $member = $this->buildMemberFromForm();
                if ($this->addNewMember($member)) {
                    $_SESSION[self::KEY_CONNECTED_MEMBER] = $member;
                    $this->showWelcomePage();
                } else {
                    $this->showRegistrationPage("Cet email est déjà pris.");
                }
            }
        } else {
            $this->showRegistrationPage();
        }
    }

    /**
     * Connect a member from the connection page
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Application\Exception\HttpException
     * @throws AppException
     */
    public function connect()
    {
        if (isset($_POST[Member::KEY_EMAIL]) && isset($_POST[Member::KEY_PASSWORD])) {
            if (empty($_POST[Member::KEY_EMAIL]) || empty($_POST[Member::KEY_PASSWORD])) {
                $this->showConnectionPage("L'email et le mot de passe doivent être renseignés.");
            } else {
                $member = $this->memberManager->getFromEmail($_POST[Member::KEY_EMAIL]);

                if ($member !== null) {
                    // Brute force protection
                    $waitingTime = BruteForceProtector::canConnectAgainIn();
                    if ($waitingTime > 0) {
                        $this->showConnectionPage("Vous vous êtes trompé trop souvent. Attendez un moment pour réfléchir.<br>Temps restant : $waitingTime s");

                    } elseif (password_verify($_POST[Member::KEY_PASSWORD], $member->getPassword())) {
                        $_SESSION[self::KEY_CONNECTED_MEMBER] = $member;
                        BruteForceProtector::resetTheUser();
                        header('Location: /home');
                    }
                }
                $this->showConnectionPage("Erreur dans l'email ou le mot de passe.");
            }
        } else {
            $this->showConnectionPage();
        }
    }

    /**
     * Disconnect the member
     */
    public function disconnect()
    {
        unset($_SESSION[self::KEY_CONNECTED_MEMBER]);
        header('Location: /home');
    }

    /**
     * Send an email with a link to reset a password
     *
     * @throws \Application\Exception\HttpException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws AppException
     * @throws \ReflectionException
     * @throws Exception
     */
    public function passwordLost()
    {
        if (isset($_GET[self::KEY_ACTION]) && $_GET[self::KEY_ACTION] = 'send') {
            $email = htmlspecialchars($_POST[Member::KEY_EMAIL]);
            if (!empty($email) && $this->memberManager->emailExists($email)) {
                $memberId = $this->memberManager->getId(null, $email);
                $key = new Key(['value' => random_int(0, 123456789)]);
                $this->keyManager->add($key);
                $link = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/profile-editor?id=' . $memberId . '&key=' . $key->getValue();
                $subject = 'Blog de Nicolas Renvoisé - Mot de passe perdu';
                $content = 'Bonjour, pour réinitialiser votre mot de passe, suivez ce lien : ' . $link;

                if (!MailSender::send($email, $subject, $content)) {
                    $key = $this->keyManager->get(null, $key->getValue());
                    $this->keyManager->delete($key->getId());
                    $this->showPasswordRecovery("L'email n'a pas pu être envoyé. Veuillez réessayer.");
                } else {
                    $this->showConnectionPage('Un email a été envoyé à l\'adresse ' . $email . ' pour vous permettre de réinitialiser votre mot de passe');
                }
            } else {
                $this->showPasswordRecovery('Vous devez entrer un email valide');
            }
        } else {
            $this->showPasswordRecovery('Un mail contenant la marche à suivre va vous être envoyé en remplissant ce formulaire');
        }
    }

    // Private

    /**
     * Show the connection page
     *
     * @param string|null $message
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function showConnectionPage(?string $message = null)
    {
        $this->render(self::VIEW_CONNECTION, [
            BlogController::KEY_MESSAGE => $message
        ]);
    }

    /**
     * Show the profile of a member with the ability to edit it
     *
     * @param Member|null $member
     * @param int|null $keyValue
     * @throws AppException
     * @throws HttpException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws Exception
     */
    private function showMemberProfileEditor($member = null, ?int $keyValue = null)
    {
        $availableRoles = $this->roleManager->getRoleNames();

        if (MemberController::memberConnected()) {

            if ($member === null || $member === 0) {
                $member = $_SESSION[self::KEY_CONNECTED_MEMBER];
            } elseif (!($member instanceof Member) && in_array('admin', $_SESSION[self::KEY_CONNECTED_MEMBER]->getRoles())) {
                $member = $this->memberManager->get((int) $member);
            }

            $this->render(self::VIEW_MEMBER_PROFILE_EDITOR, [
                self::KEY_MEMBER => $member,
                'availableRoles' => $availableRoles
            ]);

        } elseif ($keyValue) {
            try {
                $key = $this->keyManager->get(null, $keyValue);
                $this->keyManager->delete($key->getId());
            } catch (HttpException $e) {
                $this->showConnectionPage("La clé demandée n'existe plus. Relancez la procédure de récupération du mot de passe.");
            }
            $member = $this->memberManager->get($member->getId());
            $_SESSION[self::KEY_CONNECTED_MEMBER] = $member;

            $this->render(self::VIEW_MEMBER_PROFILE_EDITOR, [
                self::KEY_MEMBER => $member,
                'availableRoles' => $availableRoles
            ]);
        } else {
            throw new AppException('You can not edit a profile if you are not connected.');
        }
    }

    /**
     * Create a Member from a form with $_POST
     *
     * @return Member
     * @throws \Application\Exception\HttpException
     */
    private function buildMemberFromForm(): Member
    {
        $member = new Member();

        $member->setEmail(htmlspecialchars($_POST[Member::KEY_EMAIL]));

        if (isset($_POST[Member::KEY_PASSWORD]) && !empty($_POST[Member::KEY_PASSWORD])) {
            $member->setPassword(password_hash($_POST[Member::KEY_PASSWORD], PASSWORD_DEFAULT));
        }

        $member->setName(htmlspecialchars($_POST['name']));

        if (isset($_POST[Member::KEY_DESCRIPTION])) {
            $member->setDescription(htmlspecialchars($_POST[Member::KEY_DESCRIPTION]));
        }

        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $member->setId((int) $_POST['id']);
        } elseif (self::memberConnected()) {
            $member->setId($_SESSION[self::KEY_CONNECTED_MEMBER]->getId());
        }

        if (isset($_POST['roles'])) {
            $roles = [];
            foreach ($_POST['roles'] as $role) {
                if ($this->roleManager->isValid($role)) {
                    $roles[] = $role;
                }
            }

            if (empty($roles)) {
                $roles = [self::KEY_MEMBER];
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
     * @throws \Application\Exception\HttpException
     * @throws Exception
     */
    private function addNewMember(Member $member): bool
    {
        if ($this->memberManager->isNewMember($member)) {
            $member->setRoles([self::KEY_MEMBER]);
            $this->memberManager->add($member);

            return true;
        }

        return false;
    }

    /**
     * Check if a role is in the authorized roles
     *
     * @param array $rolesToCheck
     * @param array $authorizedRoles
     * @return bool
     */
    private static function hasAuthorizedRole(array $rolesToCheck, array $authorizedRoles)
    {
        foreach ($rolesToCheck as $roleToCheck) {
            if (in_array($roleToCheck, $authorizedRoles)) {
                return true;
            }
        }
        return false;
    }
}