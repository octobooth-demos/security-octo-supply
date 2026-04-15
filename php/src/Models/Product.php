<?php

namespace OctoCat\SupplierPortal\Models;

class Product
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetchOne(
            'SELECT p.*, s.company_name as supplier_name
             FROM products p
             JOIN suppliers s ON p.supplier_id = s.id
             WHERE p.id = ?',
            [$id]
        );
    }

    public function findBySupplierId(int $supplierId, int $limit = 50, int $offset = 0): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM products
             WHERE supplier_id = ?
             ORDER BY name ASC
             LIMIT ? OFFSET ?',
            [$supplierId, $limit, $offset]
        );
    }

    public function findAll(int $limit = 50, int $offset = 0): array
    {
        return $this->db->fetchAll(
            'SELECT p.*, s.company_name as supplier_name
             FROM products p
             JOIN suppliers s ON p.supplier_id = s.id
             ORDER BY p.name ASC
             LIMIT ? OFFSET ?',
            [$limit, $offset]
        );
    }

    public function findByCategory(string $category, int $limit = 50): array
    {
        return $this->db->fetchAll(
            'SELECT p.*, s.company_name as supplier_name
             FROM products p
             JOIN suppliers s ON p.supplier_id = s.id
             WHERE p.category = ?
             ORDER BY p.name ASC
             LIMIT ?',
            [$category, $limit]
        );
    }

    public function create(array $data): int
    {
        return $this->db->insert('products', [
            'supplier_id' => $data['supplier_id'],
            'sku' => $data['sku'],
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'category' => $data['category'],
            'unit_price' => $data['unit_price'],
            'quantity_available' => $data['quantity_available'] ?? 0,
            'minimum_order' => $data['minimum_order'] ?? 1,
            'lead_time_days' => $data['lead_time_days'] ?? 7,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function update(int $id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update('products', $data, 'id = ?', [$id]);
    }

    public function delete(int $id): int
    {
        return $this->db->delete('products', 'id = ?', [$id]);
    }

    public function updateStock(int $id, int $quantityChange): int
    {
        $product = $this->findById($id);
        if (!$product) {
            throw new \RuntimeException("Product not found: {$id}");
        }

        $newQuantity = $product['quantity_available'] + $quantityChange;
        if ($newQuantity < 0) {
            throw new \InvalidArgumentException('Insufficient stock');
        }

        return $this->update($id, ['quantity_available' => $newQuantity]);
    }

    public function searchProducts(string $query, ?int $supplierId = null): array
    {
        $params = [];
        $sql = 'SELECT p.*, s.company_name as supplier_name
                FROM products p
                JOIN suppliers s ON p.supplier_id = s.id
                WHERE (p.name LIKE ? OR p.sku LIKE ? OR p.description LIKE ?)';
        $likeQuery = "%{$query}%";
        $params = [$likeQuery, $likeQuery, $likeQuery];

        if ($supplierId !== null) {
            $sql .= ' AND p.supplier_id = ?';
            $params[] = $supplierId;
        }

        $sql .= ' ORDER BY p.name ASC LIMIT 50';

        return $this->db->fetchAll($sql, $params);
    }

    public function getLowStockProducts(int $threshold = 10): array
    {
        return $this->db->fetchAll(
            'SELECT p.*, s.company_name as supplier_name
             FROM products p
             JOIN suppliers s ON p.supplier_id = s.id
             WHERE p.quantity_available <= ? AND p.is_active = 1
             ORDER BY p.quantity_available ASC',
            [$threshold]
        );
    }

    public function getCategories(): array
    {
        return $this->db->fetchAll(
            'SELECT DISTINCT category, COUNT(*) as product_count
             FROM products
             GROUP BY category
             ORDER BY category ASC'
        );
    }

    public function getProductsByPriceRange(float $minPrice, float $maxPrice): array
    {
        return $this->db->fetchAll(
            'SELECT p.*, s.company_name as supplier_name
             FROM products p
             JOIN suppliers s ON p.supplier_id = s.id
             WHERE p.unit_price BETWEEN ? AND ? AND p.is_active = 1
             ORDER BY p.unit_price ASC',
            [$minPrice, $maxPrice]
        );
    }
}
