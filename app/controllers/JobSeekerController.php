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
}