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

    // 3. Verifies that a concrete employer/jobseeker entity row exists for the active session user
    public static function requireCompleteEntity()
    {
        self::requireLogin();
        
        $db = Database::getInstance()->getConnection();
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['role'];

        if ($role === 'employer') {
            $stmt = $db->prepare("SELECT employer_id FROM employers WHERE user_id = :user_id LIMIT 1");
        } elseif ($role === 'jobseeker') {
            $stmt = $db->prepare("SELECT jobseeker_id FROM job_seekers WHERE user_id = :user_id LIMIT 1");
        } else {
            die("Security Violation: Invalid Role.");
        }

        $stmt->execute([':user_id' => $userId]);
        
        if (!$stmt->fetch()) {
            // The entity row does not exist. Trap them in onboarding.
            header("Location: /sikaphub_v2/onboarding");
            exit();
        }
    }
}