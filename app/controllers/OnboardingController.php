<?php

require_once BASE_PATH . 'app/core/Controller.php';
require_once BASE_PATH . 'app/helpers/AuthGuard.php';

class OnboardingController extends Controller
{

    public function index()
    {
        // Only logged-in users can onboard
        AuthGuard::requireLogin();

        // If they are already active, kick them out of the onboarding wizard
        if ($_SESSION['account_status'] === 'Active') {
            die("Your account is already fully active. <a href='/sikaphub_v2/build-profile'>Go to Profile Builder</a>");
        }

        $onboardingModel = $this->model('Onboarding');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize inputs
            $data = [
                'first_name' => htmlspecialchars(trim($_POST['first_name'])),
                'last_name' => htmlspecialchars(trim($_POST['last_name'])),
                'gender' => $_POST['gender'],
                'birthdate' => $_POST['birthdate'],
                'street_address' => htmlspecialchars(trim($_POST['street_address'])),
                'barangay_id' => (int) $_POST['barangay_id'],
                'contact_number' => htmlspecialchars(trim($_POST['contact_number']))
            ];

            if ($onboardingModel->createJobSeekerProfile($_SESSION['user_id'], $data)) {
                // Instantly update the active session so the Guard lets them pass
                $_SESSION['account_status'] = 'Active';

                echo "<h3>Profile Created Successfully!</h3>";
                echo "<p>Your account is now Active. <a href='/sikaphub_v2/build-profile'>Proceed to Step 2: Skill Builder</a></p>";
            } else {
                echo "Error saving profile. Please check your inputs.";
            }

        } else {
            // GET Request: Load the UI
            $barangays = $onboardingModel->getBarangays();
            $this->view('auth/onboarding', ['barangays' => $barangays]);
        }
    }
}