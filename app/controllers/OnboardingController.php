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
            $municipalities = $onboardingModel->getMunicipalities();
            $viewName = ($role === 'employer') ? 'auth/onboarding_employer' : 'auth/onboarding';
            $this->view($viewName, ['municipalities' => $municipalities]);
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
        // 1. Sanitize the Core Identity & Legal Payload (The NOT NULLs)
        $data = [
            'company_name'       => htmlspecialchars(trim($_POST['company_name'] ?? '')),
            'contact_person'     => htmlspecialchars(trim($_POST['contact_person'] ?? '')),
            'company_email'      => filter_var(trim($_POST['company_email'] ?? ''), FILTER_SANITIZE_EMAIL),
            'company_phone'      => htmlspecialchars(trim($_POST['company_phone'] ?? '')),
            'street_address'     => htmlspecialchars(trim($_POST['street_address'] ?? '')),
            'municipality_id'    => !empty($_POST['municipality_id']) ? (int) $_POST['municipality_id'] : null,
            'postal_code'        => htmlspecialchars(trim($_POST['postal_code'] ?? '')),

            // Step 3 Optional Data
            'industry'           => htmlspecialchars(trim($_POST['industry'] ?? '')),
            'company_size'       => htmlspecialchars(trim($_POST['company_size'] ?? '')),
            'company_description'=> htmlspecialchars(trim($_POST['company_description'] ?? ''))
        ];

        try {
            // 2. Strict File Handling: Mandatory Business Permit
            if (!isset($_FILES['business_permit']) || $_FILES['business_permit']['error'] !== UPLOAD_ERR_OK) {
                header('Location: /sikaphub_v2/onboarding?error=permit_required');
                exit();
            }
            $permitDestination = BASE_PATH . 'storage/documents/';
            $permitFilename = FileUpload::secureUpload($_FILES['business_permit'], $permitDestination);

            // 3. Strict File Handling: Optional Company Logo
            $logoFilename = null;
            if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
                $logoDestination = BASE_PATH . 'storage/uploads/logos/';
                $logoFilename = FileUpload::secureUpload($_FILES['company_logo'], $logoDestination);
            }

            $data['company_logo'] = $logoFilename;

            // 4. Execute the Unified Insertion
            $onboardingModel->createEmployerProfile($_SESSION['user_id'], $data, $permitFilename);

            // Break the AuthGuard trap
            $_SESSION['account_status'] = 'Active';

            // Clean PRG (Post/Redirect/Get) redirect
            header("Location: /sikaphub_v2/employer/dashboard?success=registered");
            exit();

        } catch (PDOException $e) {
            error_log('[OnboardingController] PDOException in handleEmployerSubmit: ' . $e->getMessage());
            header('Location: /sikaphub_v2/onboarding?error=db_error');
            exit();
        } catch (RuntimeException $e) {
            error_log('[OnboardingController] RuntimeException in handleEmployerSubmit: ' . $e->getMessage());
            header('Location: /sikaphub_v2/onboarding?error=upload_error');
            exit();
        }
    }
}