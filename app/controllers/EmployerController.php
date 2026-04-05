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
}