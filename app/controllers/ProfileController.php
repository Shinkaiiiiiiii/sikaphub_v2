<?php

require_once BASE_PATH . 'app/core/Controller.php';
require_once BASE_PATH . 'app/services/AIEngineService.php';
require_once BASE_PATH . 'app/helpers/AuthGuard.php';

class ProfileController extends Controller
{
    public function buildProfile()
    {
        AuthGuard::requireLogin();

        $role = $_SESSION['role'];
        $profileModel = $this->model('Profile');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($role === 'jobseeker') {
                $this->processJobSeekerUpdate($profileModel);
            } elseif ($role === 'employer') {
                $this->processEmployerUpdate($profileModel);
            } else {
                die("Security Violation: Invalid Role.");
            }
        } else {
            if ($role === 'jobseeker') {
                $data = $this->loadJobSeekerData($profileModel);
                $this->view('profile/seeker_builder', $data);
            } else {
                $data = $this->loadEmployerData($profileModel);
                $this->view('profile/employer_builder', $data);
            }
        }
    }

    // =========================================================================
    // JOB SEEKER LOGIC ISOLATION
    // =========================================================================

    private function processJobSeekerUpdate($profileModel)
    {
        $userId = $_SESSION['user_id'];
        $existingProfile = $profileModel->getSeekerProfile($userId);

        // 1. Secure File Upload Logic (Profile Photo)
        $photoPath = $existingProfile['profile_photo'] ?? null;
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $fileTmp = $_FILES['profile_photo']['tmp_name'];
            $fileType = function_exists('mime_content_type') ? mime_content_type($fileTmp) : $_FILES['profile_photo']['type'];
            $fileSize = $_FILES['profile_photo']['size'];

            if (in_array($fileType, $allowedTypes) && $fileSize <= 2097152) {
                $ext = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
                $newFileName = uniqid('photo_', true) . '.' . $ext;
                $uploadDir = BASE_PATH . 'storage/uploads/profile_photos/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $destPath = $uploadDir . $newFileName;
                if (move_uploaded_file($fileTmp, $destPath)) $photoPath = $newFileName;
            } else {
                die("Security Violation: Invalid photo type or file exceeds 2MB limit.");
            }
        }

        // 2. Secure File Upload Logic (Resume PDF)
        $resumePath = $existingProfile['resume_file'] ?? null;
        if (isset($_FILES['resume_file']) && $_FILES['resume_file']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['application/pdf'];
            $fileTmp = $_FILES['resume_file']['tmp_name'];
            $fileType = function_exists('mime_content_type') ? mime_content_type($fileTmp) : $_FILES['resume_file']['type'];
            $fileSize = $_FILES['resume_file']['size'];

            if (in_array($fileType, $allowedTypes) && $fileSize <= 5242880) {
                $ext = pathinfo($_FILES['resume_file']['name'], PATHINFO_EXTENSION);
                $newFileName = uniqid('resume_', true) . '.' . $ext;
                $uploadDir = BASE_PATH . 'storage/uploads/resumes/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $destPath = $uploadDir . $newFileName;
                if (move_uploaded_file($fileTmp, $destPath)) $resumePath = $newFileName;
            } else {
                die("Security Violation: Invalid resume type or file exceeds 5MB limit.");
            }
        }

        // 3. Extract New UI Identity & 3NF Location Data
        $firstName = htmlspecialchars(trim($_POST['first_name'] ?? ''));
        $lastName = htmlspecialchars(trim($_POST['last_name'] ?? ''));
        $visibility = htmlspecialchars(trim($_POST['visibility'] ?? 'Public'));
        $homeMunicipalityId = isset($_POST['home_municipality_id']) && $_POST['home_municipality_id'] !== '' ? (int)$_POST['home_municipality_id'] : null;

        // 3.5 Extract Preferred Work Locations Array
        $preferredMunicipalityIds = [];
        if (!empty($_POST['preferred_municipality_ids']) && is_array($_POST['preferred_municipality_ids'])) {
            $preferredMunicipalityIds = array_map('intval', $_POST['preferred_municipality_ids']);
        }

        $preferences = [
            'desired_job_type' => htmlspecialchars(trim($_POST['desired_job_type'] ?? '')),
            'preferred_work_setup' => htmlspecialchars(trim($_POST['preferred_work_setup'] ?? 'On-site')),
            'expected_salary' => isset($_POST['expected_salary']) && $_POST['expected_salary'] !== '' ? (float) $_POST['expected_salary'] : null,
            'preferred_municipality_ids' => $preferredMunicipalityIds
        ];

        // 4. Sanitize Multidimensional Arrays (Education)
        $educationData = [];
        if (!empty($_POST['education']) && is_array($_POST['education'])) {
            foreach ($_POST['education'] as $edu) {
                if (empty(trim($edu['institution'] ?? ''))) continue;
                $educationData[] = [
                    'degree_level' => htmlspecialchars(trim($edu['degree_level'] ?? '')),
                    'school_name' => htmlspecialchars(trim($edu['institution'] ?? '')),
                    'year_graduated' => (int) ($edu['year_graduated'] ?? 0)
                ];
            }
        }

        // 5. Sanitize Multidimensional Arrays (Work Experience)
        $experienceData = [];
        if (!empty($_POST['experience']) && is_array($_POST['experience'])) {
            foreach ($_POST['experience'] as $exp) {
                if (empty(trim($exp['job_title'] ?? ''))) continue;
                $experienceData[] = [
                    'job_title' => htmlspecialchars(trim($exp['job_title'] ?? '')),
                    'company_name' => htmlspecialchars(trim($exp['company'] ?? '')),
                    'start_date' => htmlspecialchars(trim($exp['start_date'] ?? '')),
                    'end_date' => !empty($exp['end_date']) ? htmlspecialchars(trim($exp['end_date'])) : null,
                    'job_description' => ''
                ];
            }
        }

        // 6. Parse Skills (Comma-separated string from UI)
        $customSkills = [];
        if (!empty($_POST['skills'])) {
            $rawSkills = explode(',', $_POST['skills']);
            foreach ($rawSkills as $skill) {
                $cleanSkill = htmlspecialchars(trim($skill));
                if (!empty($cleanSkill)) {
                    $customSkills[] = $cleanSkill;
                }
            }
        }
        $standardSkillIds = [];

        // 7. Package Payload for Model
        $payload = [
            'user_id' => $userId,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'home_municipality_id' => $homeMunicipalityId,
            'visibility' => $visibility,
            'profile_photo' => $photoPath,
            'resume_file' => $resumePath,
            'preferences' => $preferences,
            'education' => $educationData,
            'experience' => $experienceData,
            'standard_skills' => $standardSkillIds,
            'custom_skills' => $customSkills
        ];

        // 8. Execute Transaction and Trigger AI Recompute
        if ($profileModel->saveCompleteSeekerProfile($payload)) {
            // Update active session to break the AuthGuard loop
            $_SESSION['account_status'] = 'Active';
            
            $aiService = new AIEngineService();
            $aiService->triggerMatchComputation(1, $userId);

            header("Location: /sikaphub_v2/dashboard?profile_updated=1");
            exit();
        } else {
            die("Critical Database Error: Transaction failed and was rolled back.");
        }
    }

    private function loadJobSeekerData($profileModel)
    {
        return [
            'master_skills' => $profileModel->getAllApprovedSkills(),
            'municipalities' => $profileModel->getMunicipalities(),
            'existing_profile' => $profileModel->getSeekerProfile($_SESSION['user_id'])
        ];
    }

    // =========================================================================
    // EMPLOYER LOGIC ISOLATION
    // =========================================================================

    private function processEmployerUpdate($profileModel)
    {
        $userId = $_SESSION['user_id'];
        $existingProfile = $profileModel->getEmployerProfile($userId);

        $logoPath = $existingProfile['company_logo'] ?? null;

        if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $fileTmp = $_FILES['company_logo']['tmp_name'];
            $fileType = function_exists('mime_content_type') ? mime_content_type($fileTmp) : $_FILES['company_logo']['type'];
            $fileSize = $_FILES['company_logo']['size'];

            if (in_array($fileType, $allowedTypes) && $fileSize <= 2097152) {
                $ext = pathinfo($_FILES['company_logo']['name'], PATHINFO_EXTENSION);
                $newFileName = uniqid('logo_', true) . '.' . $ext;
                $uploadDir = BASE_PATH . 'storage/uploads/logos/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $destPath = $uploadDir . $newFileName;
                if (move_uploaded_file($fileTmp, $destPath)) {
                    $logoPath = $newFileName;
                }
            } else {
                die("Security Violation: Invalid file type or file exceeds 2MB limit.");
            }
        }

        $payload = [
            'user_id' => $userId,
            'company_phone' => htmlspecialchars(trim($_POST['company_phone'] ?? '')),
            'industry' => htmlspecialchars(trim($_POST['industry'] ?? '')),
            'company_size' => htmlspecialchars(trim($_POST['company_size'] ?? '')),
            'company_logo' => $logoPath,
            'company_description' => htmlspecialchars(trim($_POST['company_description'] ?? '')),
            'website_url' => filter_var(trim($_POST['website_url'] ?? ''), FILTER_SANITIZE_URL),
            'facebook_url' => filter_var(trim($_POST['facebook_url'] ?? ''), FILTER_SANITIZE_URL),
            'linkedin_url' => filter_var(trim($_POST['linkedin_url'] ?? ''), FILTER_SANITIZE_URL),
            'twitter_url' => filter_var(trim($_POST['twitter_url'] ?? ''), FILTER_SANITIZE_URL)
        ];

        if ($profileModel->saveEmployerProfile($payload)) {
            header("Location: /sikaphub_v2/employer/dashboard?profile_updated=1");
            exit();
        } else {
            die("Critical Database Error: Employer Profile update failed.");
        }
    }

    private function loadEmployerData($profileModel)
    {
        return [
            'existing_profile' => $profileModel->getEmployerProfile($_SESSION['user_id'])
        ];
    }
}