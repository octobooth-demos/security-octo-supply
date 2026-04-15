<?php

namespace OctoCat\SupplierPortal\Controllers;

use OctoCat\SupplierPortal\Auth\SessionAuth;
use OctoCat\SupplierPortal\Models\Product;

class ProductController
{
    private Product $productModel;
    private SessionAuth $auth;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->auth = new SessionAuth();
        $this->auth->startSession();
    }

    public function index(): void
    {
        $supplierId = $this->auth->getSupplierId();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 25;
        $offset = ($page - 1) * $limit;

        $category = $_GET['category'] ?? null;
        $search = $_GET['search'] ?? null;

        if ($search) {
            $products = $this->productModel->searchProducts($search, $supplierId);
        } elseif ($category) {
            $products = $this->productModel->findByCategory($category, $limit);
        } else {
            $products = $this->productModel->findBySupplierId($supplierId, $limit, $offset);
        }

        $categories = $this->productModel->getCategories();
        $supplierName = $this->auth->getSupplierName();

        require __DIR__ . '/../../templates/products.php';
    }

    public function show(int $id): void
    {
        $product = $this->productModel->findById($id);

        if (!$product) {
            http_response_code(404);
            echo 'Product not found.';
            return;
        }

        $supplierId = $this->auth->getSupplierId();
        if ($product['supplier_id'] !== $supplierId) {
            http_response_code(403);
            echo 'Access denied.';
            return;
        }

        require __DIR__ . '/../../templates/product_detail.php';
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $categories = $this->productModel->getCategories();
            require __DIR__ . '/../../templates/product_form.php';
            return;
        }

        $supplierId = $this->auth->getSupplierId();
        $data = $this->extractProductData();
        $data['supplier_id'] = $supplierId;

        $errors = $this->validateProduct($data);
        if (!empty($errors)) {
            $categories = $this->productModel->getCategories();
            require __DIR__ . '/../../templates/product_form.php';
            return;
        }

        $id = $this->productModel->create($data);
        header("Location: /products/{$id}");
        exit;
    }

    public function update(int $id): void
    {
        $product = $this->productModel->findById($id);
        if (!$product || $product['supplier_id'] !== $this->auth->getSupplierId()) {
            http_response_code(403);
            echo 'Access denied.';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $categories = $this->productModel->getCategories();
            require __DIR__ . '/../../templates/product_form.php';
            return;
        }

        $data = $this->extractProductData();
        $errors = $this->validateProduct($data);
        if (!empty($errors)) {
            $categories = $this->productModel->getCategories();
            require __DIR__ . '/../../templates/product_form.php';
            return;
        }

        $this->productModel->update($id, $data);
        header("Location: /products/{$id}");
        exit;
    }

    public function destroy(int $id): void
    {
        $product = $this->productModel->findById($id);
        if (!$product || $product['supplier_id'] !== $this->auth->getSupplierId()) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        $this->productModel->delete($id);
        header('Location: /products');
        exit;
    }

    public function lowStock(): void
    {
        $threshold = (int) ($_GET['threshold'] ?? 10);
        $products = $this->productModel->getLowStockProducts($threshold);
        $supplierName = $this->auth->getSupplierName();

        require __DIR__ . '/../../templates/low_stock.php';
    }

    public function apiList(): void
    {
        header('Content-Type: application/json');
        $supplierId = $this->auth->getSupplierId();
        $products = $this->productModel->findBySupplierId($supplierId);
        echo json_encode(['data' => $products, 'count' => count($products)]);
    }

    public function apiUpdateStock(int $id): void
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $quantityChange = (int) ($input['quantity_change'] ?? 0);

        if ($quantityChange === 0) {
            http_response_code(400);
            echo json_encode(['error' => 'quantity_change is required and must be non-zero']);
            return;
        }

        $product = $this->productModel->findById($id);
        if (!$product || $product['supplier_id'] !== $this->auth->getSupplierId()) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }

        try {
            $this->productModel->updateStock($id, $quantityChange);
            $updated = $this->productModel->findById($id);
            echo json_encode(['data' => $updated]);
        } catch (\InvalidArgumentException $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function extractProductData(): array
    {
        return [
            'sku' => trim($_POST['sku'] ?? ''),
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'category' => trim($_POST['category'] ?? ''),
            'unit_price' => (float) ($_POST['unit_price'] ?? 0),
            'quantity_available' => (int) ($_POST['quantity_available'] ?? 0),
            'minimum_order' => (int) ($_POST['minimum_order'] ?? 1),
            'lead_time_days' => (int) ($_POST['lead_time_days'] ?? 7),
        ];
    }

    private function validateProduct(array $data): array
    {
        $errors = [];

        if (empty($data['sku'])) {
            $errors[] = 'SKU is required.';
        }
        if (empty($data['name'])) {
            $errors[] = 'Product name is required.';
        }
        if (empty($data['category'])) {
            $errors[] = 'Category is required.';
        }
        if ($data['unit_price'] <= 0) {
            $errors[] = 'Unit price must be greater than zero.';
        }
        if ($data['quantity_available'] < 0) {
            $errors[] = 'Quantity cannot be negative.';
        }

        return $errors;
    }
}
