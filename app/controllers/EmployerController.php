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

        // 3. Fetch employer core details for the Verification Gate
        $employerDetails = $employerModel->getEmployerDetails($employerId);
        $verifiedStatus = $employerDetails['verified_status'] ?? 'Pending';

        // 4. Attach the ranked applicants to each job
        // SECURITY FIX: Pass $employerId as the second argument so the model query
        // enforces ownership; an employer can only receive applicants for their own jobs.
        if (!empty($jobs)) {
            foreach ($jobs as $key => $job) {
                $applicants = $employerModel->getRankedApplicantsForJob($job['job_id'], $employerId);

                // Convert decimal scores to clean percentages for the UI
                if (!empty($applicants)) {
                    foreach ($applicants as &$applicant) {
                        $applicant['match_percentage'] = round((float) $applicant['ai_match_score'] * 100);
                    }
                }

                $jobs[$key]['applicants'] = $applicants;
            }
        }

        // 5. Render the View
        $this->view('employer/dashboard', [
            'jobs'            => $jobs ?? [],
            'verified_status' => $verifiedStatus
        ]);
    }

    public function reviewCandidate()
    {
        AuthGuard::requireActiveProfile();

        if ($_SESSION['role'] !== 'employer') {
            header("Location: /sikaphub_v2/dashboard?error=unauthorized");
            exit();
        }

        $employerModel = $this->model('Employer');
        $employerId = $employerModel->getEmployerId($_SESSION['user_id']);

        // 1. Capture ID from either GET or POST
        $appId = (int) ($_GET['app_id'] ?? $_POST['app_id'] ?? 0);

        if ($appId === 0) {
            header("Location: /sikaphub_v2/employer/dashboard?error=invalid_application");
            exit();
        }

        // 2. IDOR Firewall: Fetch application FIRST to verify ownership
        $application = $employerModel->getApplicationDetails($appId, $employerId);
        if (!$application) {
            header("Location: /sikaphub_v2/employer/dashboard?error=access_denied_or_not_found");
            exit();
        }

        // 3. Handle Status Update (POST Request)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Security Violation: Invalid CSRF token.");
            }

            $newStatus = $_POST['status'] ?? '';
            $currentStatus = $application['application_status'];
            $allowedStatuses = ['Pending', 'Reviewed', 'Accepted', 'Rejected'];

            if (!in_array($newStatus, $allowedStatuses, true)) {
                header("Location: /sikaphub_v2/employer/review-candidate?app_id=" . $appId . "&error=invalid_status");
                exit();
            }

            // State Machine Guard: Prevent redundant updates
            if ($newStatus === $currentStatus) {
                header("Location: /sikaphub_v2/employer/review-candidate?app_id=" . $appId . "&status_updated=1");
                exit();
            }

            // State Machine Guard: Prevent reverting backwards to Pending
            if ($newStatus === 'Pending' && $currentStatus !== 'Pending') {
                header("Location: /sikaphub_v2/employer/review-candidate?app_id=" . $appId . "&error=invalid_transition");
                exit();
            }

            if ($employerModel->updateApplicationStatus($appId, $employerId, $newStatus)) {
                header("Location: /sikaphub_v2/employer/review-candidate?app_id=" . $appId . "&status_updated=1");
                exit();
            } else {
                header("Location: /sikaphub_v2/employer/review-candidate?app_id=" . $appId . "&error=database_error");
                exit();
            }
        }

        // 4. Handle View Rendering (GET Request)
        $jobseekerId = $application['jobseeker_id'];
        $data = [
            'app'        => $application,
            'education'  => $employerModel->getSeekerEducation($jobseekerId),
            'experience' => $employerModel->getSeekerExperience($jobseekerId)
        ];

        $this->view('employer/review_candidate', $data);
    }
}