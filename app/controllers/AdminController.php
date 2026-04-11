<?php

require_once BASE_PATH . 'app/core/Controller.php';
require_once BASE_PATH . 'app/helpers/AuthGuard.php';

class AdminController extends Controller
{
    public function dashboard()
    {
        AuthGuard::requireLogin();
        if ($_SESSION['role'] !== 'admin')
            die("Access Denied.");

        $adminModel = $this->model('Admin');
        $data = [
            'overview' => $adminModel->getSystemOverview(),
            'geography' => $adminModel->getSeekersByBarangay(),
            'top_skills' => $adminModel->getTopDemandSkills(),
            'pending_employers' => $adminModel->getPendingEmployers(),
            'pending_skills' => $adminModel->getPendingSkills(), // NEW: Custom skills queue
            'success' => isset($_GET['success']) ? true : false
        ];
        $this->view('admin/dashboard', $data);
    }

    public function verifyEmployer()
    {
        AuthGuard::requireLogin();
        if ($_SESSION['role'] !== 'admin')
            die("Access Denied.");

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $employerId = (int) $_POST['employer_id'];
            $status = $_POST['status'];
            if (in_array($status, ['Verified', 'Rejected'])) {
                $adminModel = $this->model('Admin');
                if ($adminModel->updateEmployerVerification($employerId, $status)) {
                    header("Location: /sikaphub_v2/admin/dashboard?success=verified");
                    exit();
                }
            }
        }
    }

    // NEW: Approve Custom Skills from the Matrix
    public function approveSkill()
    {
        AuthGuard::requireLogin();
        if ($_SESSION['role'] !== 'admin')
            die("Access Denied.");

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $skillId = (int) $_POST['skill_id'];
            $action = $_POST['action']; // 'approve' or 'delete'
            $adminModel = $this->model('Admin');

            if ($action === 'approve') {
                $adminModel->updateSkillStatus($skillId, 'approved');
            } else {
                $adminModel->deleteSkill($skillId);
            }
            header("Location: /sikaphub_v2/admin/dashboard?success=skill_updated");
            exit();
        }
    }

    // SECURE GATEWAY: Serves Resumes and Profile Photos
    public function viewDocument()
    {
        AuthGuard::requireLogin(); // Only logged-in users can see files

        $file = basename($_GET['file'] ?? '');
        $type = $_GET['type'] ?? 'resume'; // 'resume' or 'photo'

        if (empty($file))
            die("File not specified.");

        // Define secure paths
        $subfolder = ($type === 'photo') ? 'profile_photos/' : 'resumes/';
        $filePath = BASE_PATH . 'storage/uploads/' . $subfolder . $file;

        if (!file_exists($filePath)) {
            die("Error 404: File not found in secure storage.");
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($filePath);

        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: inline; filename="' . $file . '"');
        readfile($filePath);
        exit();
    }
}