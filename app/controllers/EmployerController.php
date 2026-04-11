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
            header("Location: /sikaphub_v2/dashboard?error=unauthorized");
            exit();
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
        if (!empty($jobs)) {
            foreach ($jobs as $key => $job) {
                $applicants = $employerModel->getRankedApplicantsForJob($job['job_id']);

                // Convert decimal scores to clean percentages for the UI
                if (!empty($applicants)) {
                    foreach ($applicants as &$applicant) {
                        $applicant['match_percentage'] = round((float) $applicant['ai_match_score'] * 100);
                    }
                }

                $jobs[$key]['applicants'] = $applicants;
            }
        }

        // 4. Render the View
        $this->view('employer/dashboard', ['jobs' => $jobs ?? []]);
    }

    public function reviewCandidate()
    {
        // 1. Strict Security Guardrails
        AuthGuard::requireActiveProfile();

        if ($_SESSION['role'] !== 'employer') {
            header("Location: /sikaphub_v2/dashboard?error=unauthorized");
            exit();
        }

        $employerModel = $this->model('Employer');
        $employerId = $employerModel->getEmployerId($_SESSION['user_id']);

        // 2. Capture and sanitize the URL parameter
        $appId = isset($_GET['app_id']) ? (int) $_GET['app_id'] : 0;

        if ($appId === 0) {
            header("Location: /sikaphub_v2/employer/dashboard?error=invalid_application");
            exit();
        }

        // 3. Handle Status Update (POST Request)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $newStatus = $_POST['status'] ?? '';
            // Enforce ENUM constraints manually to prevent dirty data injection
            $allowedStatuses = ['Pending', 'Reviewed', 'Accepted', 'Rejected'];

            if (in_array($newStatus, $allowedStatuses)) {
                if ($employerModel->updateApplicationStatus($appId, $employerId, $newStatus)) {
                    // Sleek redirect back to the review page with a Toast flag
                    header("Location: /sikaphub_v2/employer/review-candidate?app_id=" . $appId . "&status_updated=1");
                    exit();
                } else {
                    header("Location: /sikaphub_v2/employer/review-candidate?app_id=" . $appId . "&error=database_error");
                    exit();
                }
            } else {
                header("Location: /sikaphub_v2/employer/review-candidate?app_id=" . $appId . "&error=invalid_status");
                exit();
            }
        }

        // 4. Handle View Rendering (GET Request)
        // This query acts as our IDOR Firewall. If it returns false, the employer is blocked.
        $application = $employerModel->getApplicationDetails($appId, $employerId);

        if (!$application) {
            // Log this event in the future. It could be an attacker probing URLs.
            header("Location: /sikaphub_v2/employer/dashboard?error=access_denied_or_not_found");
            exit();
        }

        // Fetch historical arrays
        $jobseekerId = $application['jobseeker_id'];
        $education = $employerModel->getSeekerEducation($jobseekerId);
        $experience = $employerModel->getSeekerExperience($jobseekerId);

        $data = [
            'app' => $application,
            'education' => $education,
            'experience' => $experience
        ];

        $this->view('employer/review_candidate', $data);
    }
}