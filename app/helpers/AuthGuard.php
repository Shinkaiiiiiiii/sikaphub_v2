<?php

class AuthGuard
{
    // 1. Forces the user to be logged in
    public static function requireLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /sikaphub_v2/login');
            exit();
        }
    }

    // 2. Traps 'Pending' users in the onboarding flow
    public static function requireActiveProfile()
    {
        self::requireLogin();

        if ($_SESSION['account_status'] === 'Pending') {
            header('Location: /sikaphub_v2/build-profile');
            exit();
        }
    }
}