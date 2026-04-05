<?php

require_once BASE_PATH . 'app/core/Controller.php';
require_once BASE_PATH . 'app/helpers/AuthGuard.php';

// 1. Import the AI Service
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

                    // 2. Execute the AI Trigger Pipeline
                    echo "<h4>Triggering AI Match Engine...</h4>";
                    $aiService = new AIEngineService();
                    $activeSeekers = $jobModel->getActiveJobSeekers();

                    echo "<div style='background:#1e1e1e; color:#00ff00; padding:15px; border-radius:5px; font-family:monospace; margin-bottom: 20px;'>";
                    echo "Initializing matrix calculation for Job ID: {$newJobId}...<br><br>";

                    $processedCount = 0;
                    foreach ($activeSeekers as $seeker) {
                        $seekerId = $seeker['jobseeker_id'];

                        // Fire the S2S cURL request to Python
                        $result = $aiService->triggerMatchComputation($newJobId, $seekerId);

                        if ($result['success']) {
                            $score = $result['data']['weighted_skill_score'];
                            // Render the live calculation output to the screen
                            echo "Seeker ID [{$seekerId}] processed. Weighted Score: <strong>{$score}</strong><br>";
                        } else {
                            echo "<span style='color:red;'>Failed to process Seeker ID [{$seekerId}]: " . htmlspecialchars($result['error']) . "</span><br>";
                        }
                        $processedCount++;
                    }
                    echo "</div>";

                    echo "<p>Successfully ranked <strong>{$processedCount}</strong> active candidates against this new opportunity.</p>";

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