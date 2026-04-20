<?php

require_once BASE_PATH . 'app/core/Controller.php';
require_once BASE_PATH . 'app/helpers/AuthGuard.php';
require_once BASE_PATH . 'app/services/AIEngineService.php'; // Retained your AI Service

class JobController extends Controller
{

    public function create()
    {
        // 1. Strict Security Guardrails
        AuthGuard::requireActiveProfile();

        if ($_SESSION['role'] !== 'employer') {
            die("Access Denied: Only verified employers can post jobs.");
        }

        // 2. Verification Gate: Abort if account is not fully approved by PESO
        $employerModel = $this->model('Employer');
        $employerId = $employerModel->getEmployerId($_SESSION['user_id']);
        if ($employerId) {
            $employerDetails = $employerModel->getEmployerDetails($employerId);
            $verifiedStatus  = $employerDetails['verified_status'] ?? 'Pending';
            if ($verifiedStatus === 'Pending' || $verifiedStatus === 'Rejected') {
                header("Location: /sikaphub_v2/employer/dashboard?error=pending_verification");
                exit();
            }
        }

        $jobModel = $this->model('Job');

        // 2. HTTP Method Branching
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Retrieve Employer ID
            $employerId = $jobModel->getEmployerIdByUserId($_SESSION['user_id']);

            if (!$employerId) {
                die("Critical Error: Employer profile not found for this user.");
            }

            // Sanitize core inputs - Perfectly matching your robust V2 Database Schema
            $jobData = [
                'job_title' => htmlspecialchars(trim($_POST['job_title'] ?? '')),
                'job_description' => htmlspecialchars(trim($_POST['job_description'] ?? '')),
                'required_experience' => htmlspecialchars(trim($_POST['required_experience'] ?? '')),
                'salary_range' => htmlspecialchars(trim($_POST['salary_range'] ?? '')),
                'employment_type' => in_array($_POST['employment_type'] ?? '', ['Full-time', 'Part-time', 'Contract', 'Freelance', 'Internship']) ? $_POST['employment_type'] : 'Full-time',
                'barangay_id' => !empty($_POST['barangay_id']) ? (int) $_POST['barangay_id'] : null
            ];

            // 3. Prepare the AI Matrix Payload (Linking IDs to Requirement Types)
            $selectedSkills = [];
            if (isset($_POST['skills']) && is_array($_POST['skills'])) {
                foreach ($_POST['skills'] as $skillId) {
                    // Default to 'Mandatory' to satisfy your junction table schema
                    $reqType = isset($_POST['requirement_type'][$skillId]) ? htmlspecialchars(trim($_POST['requirement_type'][$skillId])) : 'Mandatory';
                    $selectedSkills[(int) $skillId] = $reqType;
                }
            }

            if (empty($selectedSkills)) {
                die("Validation Error: You must select at least one required skill for the AI engine.");
            }

            // 4. Execute Unified PDO Transaction
            $newJobId = $jobModel->createJobPosting($employerId, $jobData, $selectedSkills);

            if ($newJobId) {

                // 5. Execute the AI Trigger Pipeline (Silently in the background)
                $aiService = new AIEngineService();
                $activeSeekers = $jobModel->getActiveJobSeekers();
                $processedCount = 0;

                if (!empty($activeSeekers)) {
                    foreach ($activeSeekers as $seeker) {
                        $seekerId = $seeker['jobseeker_id'];
                        // Fire the S2S cURL request to Python
                        $result = $aiService->triggerMatchComputation($newJobId, $seekerId);

                        if ($result['success']) {
                            $processedCount++;
                        } else {
                            error_log("AI Engine Match Failed for Seeker ID [{$seekerId}]: " . $result['error']);
                        }
                    }
                }

                // Clean MVC Redirect: No raw HTML echoed from the Controller!
                header("Location: /sikaphub_v2/employer/dashboard?job_posted=1&ranked=" . $processedCount);
                exit();

            } else {
                die("Critical Database Error: Could not post job.");
            }

        } else {
            // GET Request: Load the UI with lookup dictionaries
            $data = [
                'barangays' => $jobModel->getBarangays(),
                'skills' => $jobModel->getMasterSkills()
            ];
            $this->view('employer/post_job', $data);
        }
    }
}