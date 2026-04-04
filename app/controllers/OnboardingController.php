<?php

require_once BASE_PATH . 'app/core/Controller.php';
require_once BASE_PATH . 'app/helpers/AuthGuard.php';
require_once BASE_PATH . 'app/helpers/FileUpload.php';

class OnboardingController extends Controller
{

    public function index()
    {
        AuthGuard::requireLogin();

        if ($_SESSION['account_status'] === 'Active') {
            $redirectTarget = ($_SESSION['role'] === 'employer') ? 'employer/dashboard' : 'build-profile';
            die("Your account is already fully active. <a href='/sikaphub_v2/{$redirectTarget}'>Go to Dashboard</a>");
        }

        $onboardingModel = $this->model('Onboarding');
        $role = $_SESSION['role'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($role === 'jobseeker') {
                $this->handleJobSeekerSubmit($onboardingModel);
            } elseif ($role === 'employer') {
                $this->handleEmployerSubmit($onboardingModel);
            }
        } else {
            // GET Request: Load the appropriate UI
            $barangays = $onboardingModel->getBarangays();
            $viewName = ($role === 'employer') ? 'auth/onboarding_employer' : 'auth/onboarding';
            $this->view($viewName, ['barangays' => $barangays]);
        }
    }

    private function handleJobSeekerSubmit($onboardingModel)
    {
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
            $_SESSION['account_status'] = 'Active';
            echo "<h3>Profile Created Successfully!</h3>";
            echo "<p><a href='/sikaphub_v2/build-profile'>Proceed to Step 2: Skill Builder</a></p>";
        } else {
            echo "Error saving profile. Please check your inputs.";
        }
    }

    private function handleEmployerSubmit($onboardingModel)
    {
        $data = [
            'company_name' => htmlspecialchars(trim($_POST['company_name'])),
            'contact_person' => htmlspecialchars(trim($_POST['contact_person'])),
            'company_email' => filter_var(trim($_POST['company_email']), FILTER_SANITIZE_EMAIL),
            'company_phone' => htmlspecialchars(trim($_POST['company_phone'])),
            'street_address' => htmlspecialchars(trim($_POST['street_address'])),
            'barangay_id' => (int) $_POST['barangay_id']
        ];

        try {
            // Point the destination to our secure storage vault
            $destination = BASE_PATH . 'storage/documents/';
            $secureFilename = FileUpload::secureUpload($_FILES['business_permit'], $destination);

            if ($onboardingModel->createEmployerProfile($_SESSION['user_id'], $data, $secureFilename)) {
                $_SESSION['account_status'] = 'Active';
                echo "<h3>Company Registered!</h3>";
                echo "<p>Your account is Active, but your verification status is Pending PESO approval.</p>";
                echo "<p>You may now <a href='/sikaphub_v2/post-job'>Post a Job</a>.</p>";
            } else {
                echo "Database Error saving employer profile.";
            }

        } catch (RuntimeException $e) {
            echo "<h3>Upload Error:</h3><p>" . $e->getMessage() . "</p>";
        }
    }
}