<?php

// Inherit from the Base Controller
require_once BASE_PATH . 'app/core/Controller.php';

class AuthController extends Controller
{

    public function register()
    {
        // Only process POST requests
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize inputs
            $username = htmlspecialchars(trim($_POST['username']));
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];
            $role = $_POST['role']; // 'jobseeker' or 'employer'

            $userModel = $this->model('User');

            if ($userModel->register($username, $email, $password, $role)) {
                if ($role === 'employer') {
                    // For new employer accounts, grab the freshly-created user and
                    // seed the session so onboarding can access $_SESSION['user_id'].
                    $newUser = $userModel->findUserByEmail($email);
                    session_regenerate_id(true);
                    $_SESSION['user_id']       = $newUser['user_id'];
                    $_SESSION['username']      = $newUser['username'];
                    $_SESSION['role']          = $newUser['role'];
                    $_SESSION['account_status'] = $newUser['account_status'];
                    header("Location: /sikaphub_v2/onboarding");
                    exit();
                }
                // For all other roles, redirect to login with a success toast.
                header("Location: /sikaphub_v2/login?success=registered");
                exit();
            } else {
                // V2 Polish: Redirect back to register with an error flag
                header("Location: /sikaphub_v2/register?error=registration_failed");
                exit();
            }
        } else {
            // If GET request, load the registration view
            $this->view('auth/register');
        }
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            $userModel = $this->model('User');
            $loggedInUser = $userModel->login($username, $password);

            if ($loggedInUser) {
                // SECURITY: Defeat Session Fixation/Hijacking
                session_regenerate_id(true);

                // Store minimal, necessary data in the session
                $_SESSION['user_id'] = $loggedInUser['user_id'];
                $_SESSION['username'] = $loggedInUser['username'];
                $_SESSION['role'] = $loggedInUser['role'];
                $_SESSION['account_status'] = $loggedInUser['account_status'];

                // AUTOMATIC UX ROUTING BASED ON ROLE & STATUS (The Universal Switchboard)
                if ($_SESSION['account_status'] === 'Pending') {
                    header("Location: /sikaphub_v2/build-profile");
                    exit();
                }

                if ($_SESSION['role'] === 'admin') {
                    header("Location: /sikaphub_v2/admin/dashboard");
                    exit();
                } elseif ($_SESSION['role'] === 'employer') {
                    // Check whether an employer profile record already exists.
                    $employerModel = $this->model('Employer');
                    $employerRow   = $employerModel->findByUserId($_SESSION['user_id']);
                    if (!$employerRow) {
                        // No employer record → send to onboarding wizard.
                        header("Location: /sikaphub_v2/onboarding");
                    } else {
                        // Employer profile complete → go straight to dashboard.
                        header("Location: /sikaphub_v2/employer/dashboard");
                    }
                    exit();
                } elseif ($_SESSION['role'] === 'jobseeker') {
                    header("Location: /sikaphub_v2/dashboard");
                    exit();
                }

            } else {
                // V2 Polish: Redirect back to login with an error flag
                header("Location: /sikaphub_v2/login?error=invalid_credentials");
                exit();
            }
        } else {
            // If GET request, load the login view
            $this->view('auth/login');
        }
    }

    public function logout()
    {
        // Unset all session variables
        $_SESSION = array();

        // Destroy the session cookie securely
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Destroy the session entirely
        session_destroy();

        // Redirect back to login page with a success flag
        header("Location: /sikaphub_v2/login?success=logged_out");
        exit();
    }
}