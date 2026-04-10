<?php

require_once BASE_PATH . 'app/core/Controller.php';
require_once BASE_PATH . 'app/services/AIEngineService.php'; // Retained your AI Service
require_once BASE_PATH . 'app/helpers/AuthGuard.php';

class ProfileController extends Controller
{

    public function buildProfile()
    { // Kept your original method name so routing doesn't break
        // 1. Global Security Check
        AuthGuard::requireActiveProfile();

        $role = $_SESSION['role'];
        $profileModel = $this->model('Profile');

        // 2. HTTP Method Branching
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($role === 'jobseeker') {
                $this->processJobSeekerUpdate($profileModel);
            } elseif ($role === 'employer') {
                $this->processEmployerUpdate($profileModel);
            } else {
                die("Security Violation: Invalid Role.");
            }
        } else {
            // GET Request: Load the isolated UI Views
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

        // 1. Sanitize Core Preferences
        $preferences = [
            'desired_job_type' => htmlspecialchars(trim($_POST['desired_job_type'] ?? '')),
            'industry' => htmlspecialchars(trim($_POST['industry'] ?? '')),
            'expected_salary' => isset($_POST['expected_salary']) ? (float) $_POST['expected_salary'] : null,
            'preferred_work_setup' => in_array($_POST['preferred_work_setup'] ?? '', ['Remote', 'On-site', 'Hybrid']) ? $_POST['preferred_work_setup'] : 'On-site',
            'preferred_barangay_id' => !empty($_POST['preferred_barangay_id']) ? (int) $_POST['preferred_barangay_id'] : null
        ];

        // 2. Sanitize Multidimensional Arrays (Education)
        $educationData = [];
        if (!empty($_POST['education']) && is_array($_POST['education'])) {
            foreach ($_POST['education'] as $edu) {
                if (empty(trim($edu['school_name'] ?? '')))
                    continue;
                $educationData[] = [
                    'degree_level' => htmlspecialchars(trim($edu['degree_level'] ?? '')),
                    'school_name' => htmlspecialchars(trim($edu['school_name'] ?? '')),
                    'year_graduated' => (int) ($edu['year_graduated'] ?? 0)
                ];
            }
        }

        // 3. Sanitize Multidimensional Arrays (Work Experience)
        $experienceData = [];
        if (!empty($_POST['experience']) && is_array($_POST['experience'])) {
            foreach ($_POST['experience'] as $exp) {
                if (empty(trim($exp['job_title'] ?? '')))
                    continue;
                $experienceData[] = [
                    'job_title' => htmlspecialchars(trim($exp['job_title'] ?? '')),
                    'company_name' => htmlspecialchars(trim($exp['company_name'] ?? '')),
                    'start_date' => htmlspecialchars(trim($exp['start_date'] ?? '')),
                    'end_date' => !empty($exp['end_date']) ? htmlspecialchars(trim($exp['end_date'])) : null,
                    'job_description' => htmlspecialchars(trim($exp['job_description'] ?? ''))
                ];
            }
        }

        // 4. Extract Standard Skills and Custom Skills
        $standardSkillIds = isset($_POST['skills']) && is_array($_POST['skills']) ? array_map('intval', $_POST['skills']) : [];

        $customSkills = [];
        if (!empty($_POST['custom_skills'])) {
            $rawCustom = explode(',', $_POST['custom_skills']);
            foreach ($rawCustom as $skill) {
                $cleanSkill = htmlspecialchars(trim($skill));
                if (!empty($cleanSkill)) {
                    $customSkills[] = $cleanSkill;
                }
            }
        }

        // 5. Package Payload
        $payload = [
            'user_id' => $userId,
            'preferences' => $preferences,
            'education' => $educationData,
            'experience' => $experienceData,
            'standard_skills' => $standardSkillIds,
            'custom_skills' => $customSkills
        ];

        // 6. Execute Transaction and Trigger AI Recompute
        if ($profileModel->saveCompleteSeekerProfile($payload)) {

            // PHASE 4 TRIGGER RETAINED: The exact moment data is saved, call the AI
            $aiService = new AIEngineService();
            // In a production environment, you would loop through all active jobs here
            // We hardcode job_id 1 to verify the trigger pipeline as per baseline
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
            'barangays' => $profileModel->getBarangays(),
            'existing_profile' => $profileModel->getSeekerProfile($_SESSION['user_id'])
        ];
    }

    // =========================================================================
    // EMPLOYER LOGIC ISOLATION
    // =========================================================================

    private function processEmployerUpdate($profileModel)
    {
        $userId = $_SESSION['user_id'];

        $payload = [
            'user_id' => $userId,
            'company_description' => htmlspecialchars(trim($_POST['company_description'] ?? '')),
            'website_url' => filter_var(trim($_POST['website_url'] ?? ''), FILTER_SANITIZE_URL)
        ];

        if ($profileModel->saveEmployerProfile($payload)) {
            header("Location: /sikaphub_v2/employer/dashboard?profile_updated=1");
            exit();
        } else {
            die("Critical Database Error: Update failed.");
        }
    }

    private function loadEmployerData($profileModel)
    {
        return [
            'existing_profile' => $profileModel->getEmployerProfile($_SESSION['user_id'])
        ];
    }
}