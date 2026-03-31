<?php
// 1. Initialize Security & Session
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1); // Change to 0 in production

// 2. Define Absolute Paths
define('BASE_PATH', dirname(__DIR__) . '/');

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

// 8. Dispatch the Request
$method = $_SERVER['REQUEST_METHOD'];
$router->dispatch($requestUri, $method);