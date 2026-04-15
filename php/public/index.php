<?php

/**
 * OctoCAT Supplier Portal - Front Controller
 *
 * Routes all requests through a simple dispatcher.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use OctoCat\SupplierPortal\Controllers\AuthController;
use OctoCat\SupplierPortal\Controllers\ProductController;
use OctoCat\SupplierPortal\Controllers\SupplierController;
use OctoCat\SupplierPortal\Controllers\ReportController;
use OctoCat\SupplierPortal\Middleware\AuthMiddleware;

$config = require __DIR__ . '/../config/app.php';
date_default_timezone_set($config['timezone']);

if ($config['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

$middleware = new AuthMiddleware();
$middleware->handle();

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Simple router
$routes = [
    // Auth routes
    'GET /login' => [AuthController::class, 'showLogin'],
    'POST /login' => [AuthController::class, 'login'],
    'GET /logout' => [AuthController::class, 'logout'],
    'GET /register' => [AuthController::class, 'showRegister'],
    'POST /register' => [AuthController::class, 'register'],

    // Dashboard
    'GET /' => [SupplierController::class, 'dashboard'],
    'GET /dashboard' => [SupplierController::class, 'dashboard'],

    // Products
    'GET /products' => [ProductController::class, 'index'],
    'GET /products/create' => [ProductController::class, 'create'],
    'POST /products/create' => [ProductController::class, 'create'],
    'GET /products/low-stock' => [ProductController::class, 'lowStock'],

    // Profile
    'GET /profile' => [SupplierController::class, 'profile'],
    'POST /profile' => [SupplierController::class, 'updateProfile'],
    'POST /profile/password' => [SupplierController::class, 'changePassword'],

    // Reports
    'GET /reports' => [ReportController::class, 'index'],
    'GET /reports/export' => [ReportController::class, 'exportProducts'],
    'GET /reports/inventory' => [ReportController::class, 'inventorySummary'],
    'GET /reports/pricing' => [ReportController::class, 'priceAnalysis'],
    'GET /reports/orders' => [ReportController::class, 'exportOrderHistory'],

    // API endpoints
    'GET /api/products' => [ProductController::class, 'apiList'],
    'GET /api/profile' => [SupplierController::class, 'apiProfile'],

    // Health check
    'GET /health' => function () {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'service' => 'supplier-portal',
            'timestamp' => date('c'),
        ]);
    },
];

$routeKey = "{$requestMethod} {$requestUri}";

// Check exact match first
if (isset($routes[$routeKey])) {
    $handler = $routes[$routeKey];

    if ($handler instanceof \Closure) {
        $handler();
    } else {
        [$controllerClass, $method] = $handler;
        $controller = new $controllerClass();
        $controller->$method();
    }
    exit;
}

// Check parameterized routes (e.g., /products/{id})
$paramRoutes = [
    '#^GET /products/(\d+)$#' => [ProductController::class, 'show'],
    '#^GET /products/(\d+)/edit$#' => [ProductController::class, 'update'],
    '#^POST /products/(\d+)/edit$#' => [ProductController::class, 'update'],
    '#^POST /products/(\d+)/delete$#' => [ProductController::class, 'destroy'],
    '#^PATCH /api/products/(\d+)/stock$#' => [ProductController::class, 'apiUpdateStock'],
];

foreach ($paramRoutes as $pattern => $handler) {
    $subject = "{$requestMethod} {$requestUri}";
    if (preg_match($pattern, $subject, $matches)) {
        [$controllerClass, $method] = $handler;
        $controller = new $controllerClass();
        $controller->$method((int) $matches[1]);
        exit;
    }
}

// 404
http_response_code(404);
echo '<h1>404 - Page Not Found</h1>';
echo '<p>The requested page could not be found.</p>';
echo '<a href="/">Return to Dashboard</a>';
