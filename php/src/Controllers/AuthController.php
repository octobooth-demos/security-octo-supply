<?php

namespace OctoCat\SupplierPortal\Controllers;

use OctoCat\SupplierPortal\Auth\SessionAuth;

class AuthController
{
    private SessionAuth $auth;

    public function __construct()
    {
        $this->auth = new SessionAuth();
        $this->auth->startSession();
    }

    public function showLogin(): void
    {
        if ($this->auth->isAuthenticated()) {
            header('Location: /products');
            exit;
        }

        $error = null;
        if (isset($_GET['expired'])) {
            $error = 'Your session has expired. Please log in again.';
        }

        require __DIR__ . '/../../templates/login.php';
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = 'Email and password are required.';
            require __DIR__ . '/../../templates/login.php';
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
            require __DIR__ . '/../../templates/login.php';
            return;
        }

        if ($this->auth->attempt($email, $password)) {
            header('Location: /products');
            exit;
        }

        $error = 'Invalid email or password.';
        require __DIR__ . '/../../templates/login.php';
    }

    public function logout(): void
    {
        $this->auth->logout();
        header('Location: /login');
        exit;
    }

    public function showRegister(): void
    {
        require __DIR__ . '/../../templates/register.php';
    }

    public function register(): void
    {
        $data = [
            'company_name' => trim($_POST['company_name'] ?? ''),
            'contact_name' => trim($_POST['contact_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'password' => $_POST['password'] ?? '',
        ];

        $errors = $this->validateRegistration($data);
        if (!empty($errors)) {
            require __DIR__ . '/../../templates/register.php';
            return;
        }

        $supplierModel = new \OctoCat\SupplierPortal\Models\Supplier();

        $existing = $supplierModel->findByEmail($data['email']);
        if ($existing) {
            $errors[] = 'An account with this email already exists.';
            require __DIR__ . '/../../templates/register.php';
            return;
        }

        $supplierModel->create($data);

        header('Location: /login?registered=1');
        exit;
    }

    private function validateRegistration(array $data): array
    {
        $errors = [];

        if (empty($data['company_name'])) {
            $errors[] = 'Company name is required.';
        }
        if (empty($data['contact_name'])) {
            $errors[] = 'Contact name is required.';
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email address is required.';
        }
        if (strlen($data['password']) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }

        return $errors;
    }
}
