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
        $userId = $_SESSION['user_id'];

        // V2 FIX: Fetch the actual JobSeeker PK, not the User PK
        $jobseekerId = $jobSeekerModel->getJobseekerIdByUserId($userId);
        if (!$jobseekerId) {
            die("Critical Error: Job Seeker profile not found for this user.");
        }

        // 2. Fetch all open jobs from MySQL
        $openJobs = $jobSeekerModel->getAllOpenJobs();
        $scoredJobs = [];

        // 3. The N+1 AI Trigger Loop
        if (!empty($openJobs)) {
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
        }

        // 5. Render the View
        $this->view('jobseeker/dashboard', ['jobs' => $scoredJobs]);
    }

    public function apply()
    {
        // 1. Strict Security Guardrails
        AuthGuard::requireActiveProfile();

        if ($_SESSION['role'] !== 'jobseeker') {
            header("Location: /sikaphub_v2/dashboard?error=unauthorized");
            exit();
        }

        // 2. Enforce POST request to prevent CSRF via URL manipulation
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /sikaphub_v2/dashboard");
            exit();
        }

        $jobId = isset($_POST['job_id']) ? (int) $_POST['job_id'] : 0;
        $userId = $_SESSION['user_id'];

        if ($jobId === 0) {
            header("Location: /sikaphub_v2/dashboard?error=invalid_job");
            exit();
        }

        $jobSeekerModel = $this->model('JobSeeker');

        // V2 FIX: Map the User ID to the Relational JobSeeker ID
        $jobseekerId = $jobSeekerModel->getJobseekerIdByUserId($userId);

        // 3. Graceful Duplicate Check (No more dead-end echoes)
        if ($jobSeekerModel->hasAlreadyApplied($jobseekerId, $jobId)) {
            header("Location: /sikaphub_v2/dashboard?error=already_applied");
            exit();
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
            // Sleek redirect to the tracker with a success flag
            header("Location: /sikaphub_v2/my-applications?success=applied");
            exit();
        } else {
            header("Location: /sikaphub_v2/dashboard?error=system_error");
            exit();
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
        $userId = $_SESSION['user_id'];

        // V2 FIX: Fetch actual JobSeeker PK
        $jobseekerId = $jobSeekerModel->getJobseekerIdByUserId($userId);

        // 2. Fetch historical applications using the relational ID
        $applications = $jobSeekerModel->getMyApplications($jobseekerId);

        // 3. Format the scores for the UI
        if (!empty($applications)) {
            foreach ($applications as &$app) {
                $app['match_percentage'] = round((float) $app['ai_match_score'] * 100);
            }
        }

        // 4. Render the View
        $this->view('jobseeker/tracker', ['applications' => $applications ?? []]);
    }
}