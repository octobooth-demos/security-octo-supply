<?php

namespace OctoCat\SupplierPortal\Middleware;

use OctoCat\SupplierPortal\Auth\SessionAuth;

class AuthMiddleware
{
    private SessionAuth $auth;

    public function __construct()
    {
        $this->auth = new SessionAuth();
    }

    public function handle(): bool
    {
        $this->auth->startSession();

        $publicRoutes = ['/login', '/register', '/forgot-password', '/health'];
        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (in_array($currentPath, $publicRoutes, true)) {
            return true;
        }

        if (str_starts_with($currentPath, '/api/')) {
            return $this->handleApiAuth();
        }

        if (!$this->auth->isAuthenticated()) {
            header('Location: /login');
            exit;
        }

        if ($this->isSessionExpired()) {
            $this->auth->logout();
            header('Location: /login?expired=1');
            exit;
        }

        return true;
    }

    private function handleApiAuth(): bool
    {
        if (!$this->auth->isAuthenticated()) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Authentication required']);
            exit;
        }

        return true;
    }

    private function isSessionExpired(): bool
    {
        $config = require __DIR__ . '/../../config/app.php';
        $lifetime = $config['session']['lifetime'];

        if (!isset($_SESSION['login_time'])) {
            return true;
        }

        return (time() - $_SESSION['login_time']) > $lifetime;
    }
}
