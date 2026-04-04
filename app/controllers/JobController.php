<?php

require_once BASE_PATH . 'app/core/Controller.php';
require_once BASE_PATH . 'app/helpers/AuthGuard.php';

class JobController extends Controller
{

    public function create()
    {
        // 1. Strict Security Guardrails
        AuthGuard::requireActiveProfile();

        if ($_SESSION['role'] !== 'employer') {
            die("Access Denied: Only verified employers can post jobs.");
        }

        $jobModel = $this->model('Job');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Retrieve Employer ID
            $employerId = $jobModel->getEmployerIdByUserId($_SESSION['user_id']);

            if (!$employerId) {
                die("Critical Error: Employer profile not found for this user.");
            }

            // Sanitize core inputs
            $jobData = [
                'job_title' => htmlspecialchars(trim($_POST['job_title'])),
                'job_description' => htmlspecialchars(trim($_POST['job_description'])),
                'required_experience' => htmlspecialchars(trim($_POST['required_experience'])),
                'salary_range' => htmlspecialchars(trim($_POST['salary_range'])),
                'employment_type' => $_POST['employment_type'],
                'barangay_id' => (int) $_POST['barangay_id']
            ];

            // Retrieve array of selected skill IDs
            $selectedSkills = isset($_POST['skills']) ? $_POST['skills'] : [];

            if (empty($selectedSkills)) {
                echo "Validation Error: You must select at least one required skill for the AI engine.";
            } else {
                $newJobId = $jobModel->createJobPosting($employerId, $jobData, $selectedSkills);

                if ($newJobId) {
                    echo "<h3>Job Posted Successfully!</h3>";
                    echo "<p>Job ID: " . htmlspecialchars($newJobId) . "</p>";
                    // Note: In Part 3, we will inject the Python AI Trigger here.
                } else {
                    echo "Database Error: Could not post job.";
                }
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