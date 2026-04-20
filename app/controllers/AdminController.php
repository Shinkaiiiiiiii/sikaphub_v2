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

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /sikaphub_v2/admin/dashboard");
            exit();
        }

        $skillId = (int) ($_POST['skill_id'] ?? 0);
        $action  = $_POST['action'] ?? ''; // 'approve' or 'delete'

        if ($skillId <= 0 || !in_array($action, ['approve', 'delete'])) {
            header("Location: /sikaphub_v2/admin/dashboard?error=invalid_id");
            exit();
        }

        $adminModel = $this->model('Admin');

        if ($action === 'approve') {
            $adminModel->updateSkillStatus($skillId, 'approved');
            header("Location: /sikaphub_v2/admin/dashboard?success=skill_approved");
        } else {
            $adminModel->deleteSkill($skillId);
            header("Location: /sikaphub_v2/admin/dashboard?success=skill_deleted");
        }
        exit();
    }

    // SECURE GATEWAY: Serves Resumes, Profile Photos, and Business Permits
    public function viewDocument()
    {
        AuthGuard::requireLogin(); // Only logged-in users can see files

        // basename() strips any directory traversal sequences (e.g. ../)
        $file = basename($_GET['file'] ?? '');
        $type = $_GET['type'] ?? 'resume'; // 'resume', 'photo', or 'document'

        if (empty($file))
            die("File not specified.");

        // Build an ordered list of candidate paths to search securely.
        // basename() has already sanitised $file, so no traversal is possible.
        $candidatePaths = [
            BASE_PATH . 'storage/uploads/resumes/'        . $file,
            BASE_PATH . 'storage/uploads/profile_photos/' . $file,
            BASE_PATH . 'storage/documents/'              . $file,
        ];

        // If the caller supplied a type hint, check that bucket first for speed.
        if ($type === 'photo') {
            $candidatePaths = [
                BASE_PATH . 'storage/uploads/profile_photos/' . $file,
                BASE_PATH . 'storage/uploads/resumes/'        . $file,
                BASE_PATH . 'storage/documents/'              . $file,
            ];
        } elseif ($type === 'document') {
            $candidatePaths = [
                BASE_PATH . 'storage/documents/'              . $file,
                BASE_PATH . 'storage/uploads/resumes/'        . $file,
                BASE_PATH . 'storage/uploads/profile_photos/' . $file,
            ];
        }

        $resolvedPath = null;
        foreach ($candidatePaths as $candidate) {
            if (file_exists($candidate)) {
                $resolvedPath = $candidate;
                break;
            }
        }

        if ($resolvedPath === null) {
            die("Error 404: File not found in secure storage.");
        }

        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($resolvedPath);

        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: inline; filename="' . $file . '"');
        readfile($resolvedPath);
        exit();
    }

    // -------------------------------------------------------------------------
    // PLACEHOLDER: PDF Export (queued for Phase 6 Reporting Engine)
    // -------------------------------------------------------------------------
    public function exportPdf()
    {
        AuthGuard::requireLogin();
        if ($_SESSION['role'] !== 'admin')
            die("Access Denied.");

        header("Location: /sikaphub_v2/admin/dashboard?info=export_queued");
        exit();
    }

    // -------------------------------------------------------------------------
    // NAVBAR ROUTE PLACEHOLDERS — prevents 404s; queued for future phases
    // -------------------------------------------------------------------------
    public function employers()
    {
        AuthGuard::requireLogin();
        if ($_SESSION['role'] !== 'admin')
            die("Access Denied.");

        header("Location: /sikaphub_v2/admin/dashboard?info=under_construction");
        exit();
    }

    public function seekers()
    {
        AuthGuard::requireLogin();
        if ($_SESSION['role'] !== 'admin')
            die("Access Denied.");

        header("Location: /sikaphub_v2/admin/dashboard?info=under_construction");
        exit();
    }

    public function jobs()
    {
        AuthGuard::requireLogin();
        if ($_SESSION['role'] !== 'admin')
            die("Access Denied.");

        header("Location: /sikaphub_v2/admin/dashboard?info=under_construction");
        exit();
    }

    public function skills()
    {
        AuthGuard::requireLogin();
        if ($_SESSION['role'] !== 'admin')
            die("Access Denied.");

        header("Location: /sikaphub_v2/admin/dashboard?info=under_construction");
        exit();
    }

    public function auditLogs()
    {
        AuthGuard::requireLogin();
        if ($_SESSION['role'] !== 'admin')
            die("Access Denied.");

        header("Location: /sikaphub_v2/admin/dashboard?info=under_construction");
        exit();
    }

    // -------------------------------------------------------------------------
    // LOGOUT
    // -------------------------------------------------------------------------
    public function logout()
    {
        // Destroy the session completely before redirecting.
        session_unset();
        session_destroy();

        header("Location: /sikaphub_v2/login?success=logged_out");
        exit();
    }
}