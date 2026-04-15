<?php

namespace OctoCat\SupplierPortal\Models;

class Supplier
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetchOne(
            'SELECT * FROM suppliers WHERE id = ?',
            [$id]
        );
    }

    public function findByEmail(string $email): ?array
    {
        return $this->db->fetchOne(
            'SELECT * FROM suppliers WHERE email = ?',
            [$email]
        );
    }

    public function findAll(int $limit = 50, int $offset = 0): array
    {
        return $this->db->fetchAll(
            'SELECT id, company_name, contact_name, email, phone, status, created_at
             FROM suppliers
             ORDER BY company_name ASC
             LIMIT ? OFFSET ?',
            [$limit, $offset]
        );
    }

    public function findByStatus(string $status): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM suppliers WHERE status = ? ORDER BY company_name ASC',
            [$status]
        );
    }

    public function create(array $data): int
    {
        return $this->db->insert('suppliers', [
            'company_name' => $data['company_name'],
            'contact_name' => $data['contact_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'tax_id' => $data['tax_id'] ?? null,
            'status' => 'pending',
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function update(int $id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update('suppliers', $data, 'id = ?', [$id]);
    }

    public function updatePassword(int $id, string $newPassword): int
    {
        return $this->update($id, [
            'password_hash' => password_hash($newPassword, PASSWORD_BCRYPT),
        ]);
    }

    public function verifyPassword(string $email, string $password): ?array
    {
        $supplier = $this->findByEmail($email);
        if ($supplier && password_verify($password, $supplier['password_hash'])) {
            return $supplier;
        }
        return null;
    }

    public function search(string $term, int $limit = 25): array
    {
        $likeTerm = "%{$term}%";
        return $this->db->fetchAll(
            'SELECT id, company_name, contact_name, email, status
             FROM suppliers
             WHERE company_name LIKE ? OR contact_name LIKE ? OR email LIKE ?
             ORDER BY company_name ASC
             LIMIT ?',
            [$likeTerm, $likeTerm, $likeTerm, $limit]
        );
    }

    public function getStatistics(): array
    {
        return $this->db->fetchOne(
            'SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status = \'active\' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = \'pending\' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = \'suspended\' THEN 1 ELSE 0 END) as suspended
             FROM suppliers'
        );
    }
}
