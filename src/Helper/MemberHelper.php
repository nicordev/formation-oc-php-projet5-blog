<?php

namespace Helper;

use Application\Security\BruteForceProtector;
use Controller\MemberController;
use Model\Entity\Member;

class MemberHelper
{
    private function __construct()
    {
        // Disabled
    }

    /**
     * Check if there are empty fields in the registration form and fill a message
     *
     * @param array $emptyFields
     * @param string $message
     * @return bool
     */
    public static function checkEmptyRegistrationFields(array &$emptyFields, string &$message)
    {
        $isClear = true;

        if (empty($_POST[Member::KEY_NAME])) {
            $isClear = false;
            $emptyFields[] = Member::KEY_NAME;
            empty($message) ? $message = "Il manque le nom" : $message .= "<br>Il manque le nom";
        }

        if (empty($_POST[Member::KEY_EMAIL])) {
            $isClear = false;
            $emptyFields[] = Member::KEY_EMAIL;
            empty($message) ? $message = "Il manque l'email" : $message .= "<br>Il manque l'email";
        }

        if (empty($_POST[Member::KEY_PASSWORD])) {
            $isClear = false;
            $emptyFields[] = Member::KEY_PASSWORD;
            empty($message) ? $message = "Vous devez entrer un mot de passe" : $message .= "<br>Vous devez entrer un un mot de passe";
        }

        return $isClear;
    }

    /**
     * Set an array with the wrong fields and fill a message accordingly
     *
     * @param array $wrongFields
     * @param string $message
     * @param bool $isNewName
     * @param bool $isNewEmail
     */
    public static function setWrongRegistrationFields(array &$wrongFields, string &$message, bool $isNewName, bool $isNewEmail)
    {
        if (!$isNewName) {
            $message .= "Ce nom est déjà pris. ";
            $wrongFields[] = Member::KEY_NAME;
        }
        if (!$isNewEmail) {
            $message .= "Cet email est déjà pris.";
            $wrongFields[] = Member::KEY_EMAIL;
        }
    }

    /**
     * Check if there are empty fields in the connection form and fill a message
     *
     * @param array $emptyFields
     * @param string $message
     * @return bool
     */
    public static function checkEmptyConnectionFields(array &$emptyFields, string &$message)
    {
        $isClear = true;

        if (empty($_POST[Member::KEY_EMAIL])) {
            $isClear = false;
            $emptyFields[] = Member::KEY_EMAIL;
            empty($message) ? $message = "Il manque l'email" : $message .= "<br>Il manque l'email";
        }

        if (empty($_POST[Member::KEY_PASSWORD])) {
            $isClear = false;
            $emptyFields[] = Member::KEY_PASSWORD;
            empty($message) ? $message = "Vous devez entrer un mot de passe" : $message .= "<br>Vous devez entrer un un mot de passe";
        }

        return $isClear;
    }
}
