<?php

require_once BASE_PATH . 'app/core/Controller.php';
require_once BASE_PATH . 'app/helpers/AuthGuard.php';

class EmployerController extends Controller
{

    public function dashboard()
    {
        // 1. Strict Security Guardrails
        AuthGuard::requireActiveProfile();

        if ($_SESSION['role'] !== 'employer') {
            die("Access Denied: Only verified employers can access the ATS.");
        }

        $employerModel = $this->model('Employer');
        $userId = $_SESSION['user_id'];

        $employerId = $employerModel->getEmployerId($userId);
        if (!$employerId) {
            die("Critical Error: Employer profile not found.");
        }

        // 2. Fetch the Employer's Jobs
        $jobs = $employerModel->getEmployerJobs($employerId);

        // 3. Attach the ranked applicants to each job
        foreach ($jobs as $key => $job) {
            $applicants = $employerModel->getRankedApplicantsForJob($job['job_id']);

            // Convert decimal scores to clean percentages for the UI
            foreach ($applicants as &$applicant) {
                $applicant['match_percentage'] = round((float) $applicant['ai_match_score'] * 100);
            }

            $jobs[$key]['applicants'] = $applicants;
        }

        // 4. Render the View
        $this->view('employer/dashboard', ['jobs' => $jobs]);
    }

    public function reviewCandidate()
    {
        // 1. Strict Security Guardrails
        AuthGuard::requireActiveProfile();

        if ($_SESSION['role'] !== 'employer') {
            die("Access Denied: Restricted to verified employers.");
        }

        $employerModel = $this->model('Employer');
        $employerId = $employerModel->getEmployerId($_SESSION['user_id']);

        // 2. Capture and sanitize the URL parameter
        $appId = isset($_GET['app_id']) ? (int) $_GET['app_id'] : 0;

        if ($appId === 0) {
            die("Invalid Application ID.");
        }

        // 3. Handle Status Update (POST Request)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $newStatus = $_POST['status'];
            // Enforce ENUM constraints manually to prevent dirty data injection
            $allowedStatuses = ['Pending', 'Reviewed', 'Accepted', 'Rejected'];

            if (in_array($newStatus, $allowedStatuses)) {
                if ($employerModel->updateApplicationStatus($appId, $employerId, $newStatus)) {
                    // Redirect back via GET to prevent form resubmission on refresh
                    header("Location: /sikaphub_v2/employer/review-candidate?app_id=" . $appId . "&success=1");
                    exit();
                } else {
                    echo "Database Error: Could not update status.";
                }
            } else {
                echo "Invalid status provided.";
            }
        }

        // 4. Handle View Rendering (GET Request)
        // This query acts as our IDOR Firewall. If it returns false, the employer is blocked.
        $application = $employerModel->getApplicationDetails($appId, $employerId);

        if (!$application) {
            // Log this event in the future. It could be an attacker probing URLs.
            die("Access Denied: Application not found or does not belong to your company.");
        }

        // Fetch historical arrays
        $jobseekerId = $application['jobseeker_id'];
        $education = $employerModel->getSeekerEducation($jobseekerId);
        $experience = $employerModel->getSeekerExperience($jobseekerId);

        $data = [
            'app' => $application,
            'education' => $education,
            'experience' => $experience,
            'success' => isset($_GET['success']) ? true : false
        ];

        $this->view('employer/review_candidate', $data);
    }
}