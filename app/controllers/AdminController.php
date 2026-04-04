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

        // Fetch all metrics
        $data = [
            'overview' => $adminModel->getSystemOverview(),
            'geography' => $adminModel->getSeekersByBarangay(),
            'top_skills' => $adminModel->getTopDemandSkills()
        ];

        // Render the UI instead of printing the raw array
        $this->view('admin/dashboard', $data);
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