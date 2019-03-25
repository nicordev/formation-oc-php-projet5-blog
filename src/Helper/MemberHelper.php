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
    public static function isClearOfEmptyFields(array &$emptyFields, string &$message)
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
}