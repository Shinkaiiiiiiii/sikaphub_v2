<?php

require_once BASE_PATH . 'app/core/Controller.php';
require_once BASE_PATH . 'app/helpers/AuthGuard.php';

// Import Dompdf classes
use Dompdf\Dompdf;
use Dompdf\Options;

class AdminController extends Controller
{

    public function dashboard()
    {
        // Enforce Strict Admin Access securely
        AuthGuard::requireLogin();
        if ($_SESSION['role'] !== 'admin') {
            die("Access Denied: Unauthorized Clearance Level.");
        }

        $adminModel = $this->model('Admin');

        // Fetch all metrics and the new pending verification queue
        $data = [
            'overview' => $adminModel->getSystemOverview(),
            'geography' => $adminModel->getSeekersByBarangay(),
            'top_skills' => $adminModel->getTopDemandSkills(),
            'pending_employers' => $adminModel->getPendingEmployers(), // NEW DATA
            'success' => isset($_GET['success']) ? true : false
        ];

        // Render the UI
        $this->view('admin/dashboard', $data);
    }

    // NEW METHOD: Process Employer Verification
    public function verifyEmployer()
    {
        AuthGuard::requireLogin();
        if ($_SESSION['role'] !== 'admin')
            die("Access Denied.");

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $employerId = (int) $_POST['employer_id'];
            $status = $_POST['status']; // Expected: 'Verified' or 'Rejected'

            $allowedStatuses = ['Verified', 'Rejected'];
            if (in_array($status, $allowedStatuses)) {
                $adminModel = $this->model('Admin');
                if ($adminModel->updateEmployerVerification($employerId, $status)) {
                    header("Location: /sikaphub_v2/admin/dashboard?success=1");
                    exit();
                } else {
                    echo "Database Error updating verification status.";
                }
            } else {
                echo "Invalid status parameter.";
            }
        }
    }

    // NEW METHOD: The Secure File Gateway
    public function viewDocument()
    {
        AuthGuard::requireLogin();
        if ($_SESSION['role'] !== 'admin')
            die("Access Denied.");

        // 1. Capture the requested filename
        $requestedFile = isset($_GET['file']) ? $_GET['file'] : '';

        if (empty($requestedFile)) {
            die("No file specified.");
        }

        // 2. PATH TRAVERSAL DEFENSE: Strip all slashes and directory commands
        $secureFilename = basename($requestedFile);

        // 3. Construct the absolute path to the isolated vault
        $filePath = BASE_PATH . 'storage/documents/' . $secureFilename;

        // 4. Verify the file actually exists
        if (!file_exists($filePath)) {
            die("Error 404: File not found in secure storage.");
        }

        // 5. Cryptographically determine the MIME type for the HTTP headers
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($filePath);

        // 6. Output HTTP Headers
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: inline; filename="' . $secureFilename . '"');
        header('Content-Length: ' . filesize($filePath));

        // Disable caching for secure documents
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');

        // 7. Stream the binary file directly to the browser
        readfile($filePath);
        exit();
    }

    public function exportPdf()
    {
        // Enforce Strict Admin Access securely
        AuthGuard::requireLogin();
        if ($_SESSION['role'] !== 'admin') {
            die("Access Denied: Unauthorized Clearance Level.");
        }

        $adminModel = $this->model('Admin');
        $geography = $adminModel->getSeekersByBarangay();
        $top_skills = $adminModel->getTopDemandSkills();

        // 1. Construct the HTML string for the PDF
        $html = "<h1>PESO Guimba - System Analytics Report</h1>";
        $html .= "<p>Generated on: " . date('Y-m-d H:i:s') . "</p>";

        $html .= "<h3>Seeker Demographics</h3><ul>";
        foreach ($geography as $geo) {
            $html .= "<li>" . htmlspecialchars($geo['barangay_name']) . ": " . htmlspecialchars($geo['seeker_count']) . "</li>";
        }
        $html .= "</ul>";

        $html .= "<h3>Top In-Demand Skills</h3><ul>";
        foreach ($top_skills as $skill) {
            $html .= "<li>" . htmlspecialchars($skill['skill_name']) . " (" . htmlspecialchars($skill['demand_count']) . " requests)</li>";
        }
        $html .= "</ul>";

        // 2. Initialize Dompdf securely
        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $options->set('isRemoteEnabled', false); // SECURITY: Prevents Dompdf from executing remote code/images

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');

        // 3. Render the HTML as PDF
        $dompdf->render();

        // 4. Force download to the admin's browser
        $dompdf->stream("PESO_Analytics_Report_" . date('Ymd') . ".pdf", ["Attachment" => true]);
    }
}