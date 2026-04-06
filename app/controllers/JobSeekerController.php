<?php

require_once BASE_PATH . 'app/core/Controller.php';
require_once BASE_PATH . 'app/helpers/AuthGuard.php';
require_once BASE_PATH . 'app/services/AIEngineService.php';

class JobSeekerController extends Controller
{

    public function dashboard()
    {
        // 1. Strict Security Guardrails
        AuthGuard::requireActiveProfile();

        if ($_SESSION['role'] !== 'jobseeker') {
            die("Access Denied: Only verified job seekers can view this dashboard.");
        }

        $jobSeekerModel = $this->model('JobSeeker');
        $aiService = new AIEngineService();
        $jobseekerId = $_SESSION['user_id'];

        // 2. Fetch all open jobs from MySQL
        $openJobs = $jobSeekerModel->getAllOpenJobs();
        $scoredJobs = [];

        // 3. The N+1 AI Trigger Loop
        foreach ($openJobs as $job) {
            $result = $aiService->triggerMatchComputation($job['job_id'], $jobseekerId);

            // Default to 0 if the Python engine fails or network drops
            $matchScore = 0.0;

            if ($result['success']) {
                $matchScore = (float) $result['data']['weighted_skill_score'];
            }

            // Inject the score into the job array
            $job['match_score'] = $matchScore;

            // Convert to a clean percentage for the UI (e.g., 0.32 becomes 32)
            $job['match_percentage'] = round($matchScore * 100);

            $scoredJobs[] = $job;
        }

        // 4. Sort the array using usort() (Highest score first)
        usort($scoredJobs, function ($a, $b) {
            // If scores are exactly equal, sort by newest date
            if ($a['match_score'] == $b['match_score']) {
                return strtotime($b['date_posted']) <=> strtotime($a['date_posted']);
            }
            // Otherwise, sort by highest match score
            return $b['match_score'] <=> $a['match_score'];
        });

        // 5. Render the View
        $this->view('jobseeker/dashboard', ['jobs' => $scoredJobs]);
    }

    public function apply()
    {
        // 1. Strict Security Guardrails
        AuthGuard::requireActiveProfile();

        if ($_SESSION['role'] !== 'jobseeker') {
            die("Access Denied: Only job seekers can apply for jobs.");
        }

        // 2. Enforce POST request to prevent CSRF via URL manipulation
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die("Method Not Allowed.");
        }

        $jobId = isset($_POST['job_id']) ? (int) $_POST['job_id'] : 0;
        $jobseekerId = $_SESSION['user_id'];

        if ($jobId === 0) {
            die("Invalid Job ID.");
        }

        $jobSeekerModel = $this->model('JobSeeker');

        // 3. Graceful Duplicate Check
        if ($jobSeekerModel->hasAlreadyApplied($jobseekerId, $jobId)) {
            echo "<h3>Application Failed</h3>";
            echo "<p>You have already applied for this position. Please wait for the employer to review your profile.</p>";
            echo "<a href='/sikaphub_v2/dashboard'>Return to Dashboard</a>";
            return;
        }

        // 4. ZERO TRUST SECURITY: Recalculate the authoritative score server-side
        $aiService = new AIEngineService();
        $result = $aiService->triggerMatchComputation($jobId, $jobseekerId);

        $authoritativeScore = 0.00;
        if ($result['success']) {
            $authoritativeScore = (float) $result['data']['weighted_skill_score'];
        }

        // 5. Execute Point-in-Time Capture
        if ($jobSeekerModel->applyForJob($jobseekerId, $jobId, $authoritativeScore)) {
            echo "<h3>Application Submitted Successfully!</h3>";
            echo "<p>Your profile and a verified Match Score of <strong>" . ($authoritativeScore * 100) . "%</strong> have been locked and sent to the employer.</p>";
            // Link updated to point to the new Tracker
            echo "<a href='/sikaphub_v2/my-applications'>View Application Tracker</a>";
        } else {
            echo "A system error occurred while processing your application.";
        }
    }

    public function tracker()
    {
        // 1. Strict Security Guardrails
        AuthGuard::requireActiveProfile();

        if ($_SESSION['role'] !== 'jobseeker') {
            die("Access Denied: Only job seekers can track applications.");
        }

        $jobSeekerModel = $this->model('JobSeeker');

        // 2. Fetch the historical applications using the authenticated User ID
        $applications = $jobSeekerModel->getMyApplications($_SESSION['user_id']);

        // 3. Format the scores for the UI
        foreach ($applications as &$app) {
            $app['match_percentage'] = round((float) $app['ai_match_score'] * 100);
        }

        // 4. Render the View
        $this->view('jobseeker/tracker', ['applications' => $applications]);
    }
}