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
                echo "Registration Successful! Account status is Pending.";
                // In the future, we will redirect to a login view here
            } else {
                echo "Registration Failed. Username or Email might already exist.";
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

                echo "Login Successful! Welcome " . $_SESSION['username'];
                // In the future, redirect to the specific dashboard based on role
            } else {
                echo "Invalid credentials.";
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

        // Destroy the session cookie
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
        echo "Logged out successfully.";
    }
}