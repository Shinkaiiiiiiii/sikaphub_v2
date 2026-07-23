<?php

require_once BASE_PATH . 'app/core/Controller.php';
require_once BASE_PATH . 'app/helpers/AuthGuard.php';
require_once BASE_PATH . 'app/services/AIEngineService.php'; 

class JobController extends Controller
{
    public function create()
    {
        // 1. Strict Security Guardrails
        AuthGuard::requireActiveProfile();

        if ($_SESSION['role'] !== 'employer') {
            die("Access Denied: Only verified employers can post jobs.");
        }

        // 2. The Hard Stop (Anti-Ghost Employer)
        $employerModel = $this->model('Employer');
        $employerId = $employerModel->getEmployerId($_SESSION['user_id']);

        if (!$employerId) {
            // They bypassed onboarding. Kill the script.
            die("Critical Error: Employer profile not found. Please complete your registration.");
        }

        // 3. The Status Gate (Single Source of Truth)
        $employerDetails = $employerModel->getEmployerDetails($employerId);
        $verifiedStatus  = $employerDetails['verified_status'] ?? 'Pending';

        // Deny by Default: If it is anything other than 'Verified', redirect them.
        if ($verifiedStatus !== 'Verified') {
            header("Location: /sikaphub_v2/employer/dashboard?error=pending_verification");
            exit();
        }

        $jobModel = $this->model('Job');

        // 4. HTTP Method Branching
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            // Note: We removed the redundant $employerId query here. 
            // We are safely reusing the $employerId validated at the top of the controller.

            // Sanitize core inputs
            $jobData = [
                'job_title' => htmlspecialchars(trim($_POST['job_title'] ?? '')),
                'job_description' => htmlspecialchars(trim($_POST['job_description'] ?? '')),
                'required_experience' => htmlspecialchars(trim($_POST['required_experience'] ?? '')),
                'salary_range' => htmlspecialchars(trim($_POST['salary_range'] ?? '')),
                'employment_type' => in_array($_POST['employment_type'] ?? '', ['Full-time', 'Part-time', 'Contract', 'Freelance', 'Internship']) ? $_POST['employment_type'] : 'Full-time',
                'municipality_id' => !empty($_POST['municipality_id']) ? (int) $_POST['municipality_id'] : null
            ];

            // 5. Prepare the AI Matrix Payload
            $selectedSkills = [];
            if (isset($_POST['skills']) && is_array($_POST['skills'])) {
                foreach ($_POST['skills'] as $skillId) {
                    $reqType = isset($_POST['requirement_type'][$skillId]) ? htmlspecialchars(trim($_POST['requirement_type'][$skillId])) : 'Mandatory';
                    $selectedSkills[(int) $skillId] = $reqType;
                }
            }

            if (empty($selectedSkills)) {
                die("Validation Error: You must select at least one required skill for the AI engine.");
            }

            // 6. Execute Unified PDO Transaction
            $newJobId = $jobModel->createJobPosting($employerId, $jobData, $selectedSkills);

            if ($newJobId) {

                // 7. Execute the AI Trigger Pipeline
                $aiService = new AIEngineService();
                $activeSeekers = $jobModel->getActiveJobSeekers();
                $processedCount = 0;

                if (!empty($activeSeekers)) {
                    foreach ($activeSeekers as $seeker) {
                        $seekerId = $seeker['jobseeker_id'];
                        
                        $result = $aiService->triggerMatchComputation($newJobId, $seekerId);

                        if ($result['success']) {
                            $processedCount++;
                        } else {
                            error_log("AI Engine Match Failed for Seeker ID [{$seekerId}]: " . $result['error']);
                        }
                    }
                }

                header("Location: /sikaphub_v2/employer/dashboard?job_posted=1&ranked=" . $processedCount);
                exit();

            } else {
                die("Critical Database Error: Could not post job.");
            }

        } else {
            // GET Request: Load the UI with lookup dictionaries
            $data = [
                'municipalities' => $jobModel->getMunicipalities(),
                'skills' => $jobModel->getMasterSkills()
            ];
            $this->view('employer/post_job', $data);
        }
    }
}