<?php

namespace OctoCat\SupplierPortal\Auth;

use OctoCat\SupplierPortal\Models\Supplier;

class SessionAuth
{
    private Supplier $supplierModel;

    public function __construct()
    {
        $this->supplierModel = new Supplier();
    }

    public function startSession(): void
    {
        $config = require __DIR__ . '/../../config/app.php';
        $sessionConfig = $config['session'];

        if (session_status() === PHP_SESSION_NONE) {
            session_name($sessionConfig['name']);
            session_set_cookie_params([
                'lifetime' => $sessionConfig['lifetime'],
                'path' => '/',
                'secure' => $sessionConfig['secure'],
                'httponly' => $sessionConfig['httponly'],
                'samesite' => 'Strict',
            ]);
            session_start();
        }
    }

    public function attempt(string $email, string $password): bool
    {
        $supplier = $this->supplierModel->verifyPassword($email, $password);

        if ($supplier === null) {
            return false;
        }

        if ($supplier['status'] !== 'active') {
            return false;
        }

        $this->setSession($supplier);
        session_regenerate_id(true);

        return true;
    }

    public function isAuthenticated(): bool
    {
        return isset($_SESSION['supplier_id']) && isset($_SESSION['authenticated']);
    }

    public function getCurrentSupplier(): ?array
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        return $this->supplierModel->findById((int) $_SESSION['supplier_id']);
    }

    public function getSupplierId(): ?int
    {
        return $this->isAuthenticated() ? (int) $_SESSION['supplier_id'] : null;
    }

    public function getSupplierName(): ?string
    {
        return $_SESSION['company_name'] ?? null;
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    public function requireAuth(): void
    {
        if (!$this->isAuthenticated()) {
            header('Location: /login');
            exit;
        }
    }

    private function setSession(array $supplier): void
    {
        $_SESSION['supplier_id'] = $supplier['id'];
        $_SESSION['company_name'] = $supplier['company_name'];
        $_SESSION['email'] = $supplier['email'];
        $_SESSION['authenticated'] = true;
        $_SESSION['login_time'] = time();
    }
}
