<?php
// 1. Initialize Security & Session
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1); // Change to 0 in production

// 2. Define Absolute Paths
define('BASE_PATH', dirname(__DIR__) . '/');

// Load Composer Dependencies
require_once BASE_PATH . 'vendor/autoload.php';

// Load Core Helpers (CSRF MUST load early)
require_once BASE_PATH . 'app/helpers/CSRF.php';
require_once BASE_PATH . 'app/helpers/AuthGuard.php';

// Initialize Global CSRF Token
CSRF::generateToken();

// 3. Simple .env Loader
function loadEnv($path)
{
    if (!file_exists($path))
        return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0)
            continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}
loadEnv(BASE_PATH . '.env');

// 4. Autoload Core Classes
require_once BASE_PATH . 'config/Database.php';
require_once BASE_PATH . 'app/core/Router.php';
require_once BASE_PATH . 'app/controllers/AuthController.php';
require_once BASE_PATH . 'app/controllers/ProfileController.php';
require_once BASE_PATH . 'app/controllers/AdminController.php';
require_once BASE_PATH . 'app/controllers/OnboardingController.php';
require_once BASE_PATH . 'app/controllers/JobController.php';
require_once BASE_PATH . 'app/controllers/JobSeekerController.php';
require_once BASE_PATH . 'app/controllers/EmployerController.php';
require_once BASE_PATH . 'app/services/AIEngineService.php';

// 5. Initialize Router
$router = new Router();

// 6. Define Base URI Path dynamically to handle subdirectory execution
$baseUri = str_replace('/public/index.php', '', $_SERVER['SCRIPT_NAME']);
$requestUri = str_replace($baseUri, '', $_SERVER['REQUEST_URI']);

// 7. Define Application Routes
$router->get('/', function () {
    echo "<h1>S.I.K.A.P. Hub V2 MVC Engine is Live!</h1>";
    echo "<a href='login'>Login</a> | <a href='register'>Register</a>";
});

// Registration Routes
$router->get('/register', ['AuthController', 'register']);
$router->post('/register', ['AuthController', 'register']);

// Login Routes
$router->get('/login', ['AuthController', 'login']);
$router->post('/login', ['AuthController', 'login']);

// Logout Route
$router->get('/logout', ['AuthController', 'logout']);

// Onboarding Routes
$router->get('/onboarding', ['OnboardingController', 'index']);
$router->post('/onboarding', ['OnboardingController', 'index']);

// Profile Routes
$router->get('/build-profile', ['ProfileController', 'buildProfile']);
$router->post('/build-profile', ['ProfileController', 'buildProfile']);

// Job Seeker Dashboard & Application Routes
$router->get('/dashboard', ['JobSeekerController', 'dashboard']);
$router->get('/my-applications', ['JobSeekerController', 'tracker']);
$router->post('/apply', ['JobSeekerController', 'apply']);

// Job Routes
$router->get('/post-job', ['JobController', 'create']);
$router->post('/post-job', ['JobController', 'create']);

// Employer ATS Dashboard & Review Routes
$router->get('/employer/dashboard', ['EmployerController', 'dashboard']);
$router->get('/employer/review-candidate', ['EmployerController', 'reviewCandidate']);
$router->post('/employer/review-candidate', ['EmployerController', 'reviewCandidate']);

// Admin Routes
$router->get('/admin/dashboard', ['AdminController', 'dashboard']);
$router->get('/admin/export', ['AdminController', 'exportPdf']);
$router->post('/admin/verify-employer', ['AdminController', 'verifyEmployer']);
$router->get('/admin/view-document', ['AdminController', 'viewDocument']);

// AI Engine Test Route
$router->get('/test-ai', function () {
    echo "<h2>Testing AI Engine S2S Connection...</h2>";

    $aiService = new AIEngineService();

    // Hardcode dummy IDs for testing purposes
    // Ensure these IDs actually exist in your database or Python will return 0.0
    $jobId = 1;
    $jobseekerId = 1;

    $result = $aiService->triggerMatchComputation($jobId, $jobseekerId);

    echo "<pre>";
    print_r($result);
    echo "</pre>";
});

// 8. Dispatch the Request
$method = $_SERVER['REQUEST_METHOD'];
$router->dispatch($requestUri, $method);