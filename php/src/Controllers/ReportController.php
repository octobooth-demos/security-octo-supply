<?php

namespace OctoCat\SupplierPortal\Controllers;

use OctoCat\SupplierPortal\Auth\SessionAuth;
use OctoCat\SupplierPortal\Models\Product;
use OctoCat\SupplierPortal\Models\Database;

class ReportController
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
        $supplierName = $this->auth->getSupplierName();
        require __DIR__ . '/../../templates/reports.php';
    }

    public function exportProducts(): void
    {
        $supplierId = $this->auth->getSupplierId();
        $format = $_GET['format'] ?? 'csv';
        $products = $this->productModel->findBySupplierId($supplierId, 1000);

        switch ($format) {
            case 'csv':
                $this->exportCsv($products);
                break;
            case 'json':
                $this->exportJson($products);
                break;
            default:
                http_response_code(400);
                echo 'Unsupported export format.';
        }
    }

    public function inventorySummary(): void
    {
        $supplierId = $this->auth->getSupplierId();
        $db = Database::getInstance();

        $summary = $db->fetchAll(
            'SELECT
                category,
                COUNT(*) as product_count,
                SUM(quantity_available) as total_stock,
                AVG(unit_price) as avg_price,
                MIN(unit_price) as min_price,
                MAX(unit_price) as max_price,
                SUM(unit_price * quantity_available) as total_value
             FROM products
             WHERE supplier_id = ?
             GROUP BY category
             ORDER BY total_value DESC',
            [$supplierId]
        );

        $totals = $db->fetchOne(
            'SELECT
                COUNT(*) as total_products,
                SUM(quantity_available) as total_stock,
                SUM(unit_price * quantity_available) as total_value
             FROM products
             WHERE supplier_id = ?',
            [$supplierId]
        );

        $supplierName = $this->auth->getSupplierName();
        require __DIR__ . '/../../templates/inventory_summary.php';
    }

    public function priceAnalysis(): void
    {
        $supplierId = $this->auth->getSupplierId();
        $db = Database::getInstance();

        $priceDistribution = $db->fetchAll(
            'SELECT
                CASE
                    WHEN unit_price < 10 THEN \'Under $10\'
                    WHEN unit_price < 50 THEN \'$10 - $49\'
                    WHEN unit_price < 100 THEN \'$50 - $99\'
                    WHEN unit_price < 500 THEN \'$100 - $499\'
                    ELSE \'$500+\'
                END as price_range,
                COUNT(*) as count,
                AVG(unit_price) as avg_price
             FROM products
             WHERE supplier_id = ?
             GROUP BY price_range
             ORDER BY MIN(unit_price) ASC',
            [$supplierId]
        );

        $supplierName = $this->auth->getSupplierName();
        require __DIR__ . '/../../templates/price_analysis.php';
    }

    public function exportOrderHistory(): void
    {
        $supplierId = $this->auth->getSupplierId();
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        $db = Database::getInstance();
        $orders = $db->fetchAll(
            'SELECT
                p.sku,
                p.name as product_name,
                p.category,
                p.unit_price,
                p.quantity_available,
                p.created_at
             FROM products p
             WHERE p.supplier_id = ?
               AND p.created_at BETWEEN ? AND ?
             ORDER BY p.created_at DESC',
            [$supplierId, $startDate, $endDate]
        );

        $this->exportCsv($orders, "order_history_{$startDate}_{$endDate}.csv");
    }

    private function exportCsv(array $data, string $filename = 'export.csv'): void
    {
        if (empty($data)) {
            http_response_code(204);
            echo 'No data to export.';
            return;
        }

        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        $output = fopen('php://output', 'w');

        // Header row
        fputcsv($output, array_keys($data[0]));

        // Data rows
        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    private function exportJson(array $data): void
    {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="export.json"');

        echo json_encode([
            'exported_at' => date('Y-m-d\TH:i:s\Z'),
            'supplier' => $this->auth->getSupplierName(),
            'record_count' => count($data),
            'data' => $data,
        ], JSON_PRETTY_PRINT);
        exit;
    }
}
