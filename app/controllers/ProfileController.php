<?php

require_once BASE_PATH . 'app/core/Controller.php';
require_once BASE_PATH . 'app/services/AIEngineService.php';
require_once BASE_PATH . 'app/helpers/AuthGuard.php';

class ProfileController extends Controller
{

    public function buildProfile()
    {
        // Enforce Strict Active Profile Authentication
        AuthGuard::requireActiveProfile();

        $profileModel = $this->model('Profile');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Retrieve array of selected skill IDs from the form
            $selectedSkills = isset($_POST['skills']) ? $_POST['skills'] : [];

            // Assume jobseeker_id maps 1:1 with user_id for this baseline
            $jobseekerId = $_SESSION['user_id'];

            if (!empty($selectedSkills)) {
                $updateSuccess = $profileModel->updateJobSeekerSkills($jobseekerId, $selectedSkills);

                if ($updateSuccess) {
                    echo "<h3>Profile Saved Successfully!</h3>";

                    // PHASE 4 TRIGGER: The exact moment data is saved, call the AI
                    echo "<p>Triggering AI Engine Recompute...</p>";
                    $aiService = new AIEngineService();

                    // In a production environment, you would loop through all active jobs here
                    // We hardcode job_id 1 to verify the trigger pipeline
                    $recomputeResult = $aiService->triggerMatchComputation(1, $jobseekerId);

                    echo "<pre>";
                    print_r($recomputeResult);
                    echo "</pre>";

                } else {
                    echo "Database Error: Transaction Rolled Back.";
                }
            } else {
                echo "Please select at least one skill.";
            }
        } else {
            // GET Request: Load the UI with the dictionary
            $masterSkills = $profileModel->fetchAllMasterSkills();
            $this->view('profile/builder', ['skills' => $masterSkills]);
        }
    }
}