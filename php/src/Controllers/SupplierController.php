<?php

namespace OctoCat\SupplierPortal\Controllers;

use OctoCat\SupplierPortal\Auth\SessionAuth;
use OctoCat\SupplierPortal\Models\Supplier;

class SupplierController
{
    private Supplier $supplierModel;
    private SessionAuth $auth;

    public function __construct()
    {
        $this->supplierModel = new Supplier();
        $this->auth = new SessionAuth();
        $this->auth->startSession();
    }

    public function profile(): void
    {
        $supplier = $this->auth->getCurrentSupplier();
        if (!$supplier) {
            header('Location: /login');
            exit;
        }

        require __DIR__ . '/../../templates/profile.php';
    }

    public function updateProfile(): void
    {
        $supplierId = $this->auth->getSupplierId();
        $data = [
            'company_name' => trim($_POST['company_name'] ?? ''),
            'contact_name' => trim($_POST['contact_name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
        ];

        $errors = [];
        if (empty($data['company_name'])) {
            $errors[] = 'Company name is required.';
        }
        if (empty($data['contact_name'])) {
            $errors[] = 'Contact name is required.';
        }

        if (!empty($errors)) {
            $supplier = $this->auth->getCurrentSupplier();
            require __DIR__ . '/../../templates/profile.php';
            return;
        }

        $this->supplierModel->update($supplierId, $data);
        $supplier = $this->auth->getCurrentSupplier();
        $success = 'Profile updated successfully.';

        require __DIR__ . '/../../templates/profile.php';
    }

    public function changePassword(): void
    {
        $supplierId = $this->auth->getSupplierId();
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $supplier = $this->auth->getCurrentSupplier();
        $errors = [];

        if (!password_verify($currentPassword, $supplier['password_hash'])) {
            $errors[] = 'Current password is incorrect.';
        }
        if (strlen($newPassword) < 8) {
            $errors[] = 'New password must be at least 8 characters.';
        }
        if ($newPassword !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }

        if (!empty($errors)) {
            require __DIR__ . '/../../templates/profile.php';
            return;
        }

        $this->supplierModel->updatePassword($supplierId, $newPassword);
        $success = 'Password changed successfully.';

        require __DIR__ . '/../../templates/profile.php';
    }

    public function dashboard(): void
    {
        $supplier = $this->auth->getCurrentSupplier();
        $stats = $this->supplierModel->getStatistics();

        $productModel = new \OctoCat\SupplierPortal\Models\Product();
        $lowStockProducts = $productModel->getLowStockProducts(10);
        $recentProducts = $productModel->findBySupplierId($supplier['id'], 5);

        require __DIR__ . '/../../templates/dashboard.php';
    }

    public function apiProfile(): void
    {
        header('Content-Type: application/json');

        $supplier = $this->auth->getCurrentSupplier();
        if (!$supplier) {
            http_response_code(401);
            echo json_encode(['error' => 'Not authenticated']);
            return;
        }

        unset($supplier['password_hash']);
        echo json_encode(['data' => $supplier]);
    }
}
