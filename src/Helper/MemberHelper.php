<?php

namespace Helper;


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
     * @param bool $hasStrongPassword
     */
    public static function setWrongRegistrationFields(array &$wrongFields, string &$message, bool $isNewName, bool $isNewEmail, bool $hasStrongPassword)
    {
        if (!$isNewName) {
            $message .= "Ce nom est déjà pris. ";
            $wrongFields[] = Member::KEY_NAME;
        }
        if (!$isNewEmail) {
            $message .= "Cet email est déjà pris.";
            $wrongFields[] = Member::KEY_EMAIL;
        }
        if (!$hasStrongPassword) {
            $message .= "Le mot de passe doit comporter au moins 8 caractères dont une lettre minuscule, une lettre majuscule, un chiffre et un caractère spécial. Bon courage ! ☺";
            $wrongFields[] = Member::KEY_PASSWORD;
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

    /**
     * Password must have at least 8 characters, 1 lower case, 1 upper case, 1 digit, 1 special character, avoid any non-whitespace character
     *
     * @param string|null $password
     * @return bool
     */
    public static function hasStrongPassword(?string $password)
    {
        if (preg_match("#^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?[!$%@\#£€*?&_])\S{8,}$#", $password)) {
            return true;
        }
        return false;
    }
}
