<?php
$pageTitle = 'Dashboard';
ob_start();
?>

<h1 style="margin-bottom: 1.5rem;">Welcome, <?= htmlspecialchars($supplier['contact_name']) ?></h1>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div class="card" style="text-align: center;">
        <div style="font-size: 2rem; font-weight: 700; color: #0366d6;"><?= $stats['total'] ?? 0 ?></div>
        <div style="color: #586069; font-size: 0.9rem;">Total Suppliers</div>
    </div>
    <div class="card" style="text-align: center;">
        <div style="font-size: 2rem; font-weight: 700; color: #22863a;"><?= $stats['active'] ?? 0 ?></div>
        <div style="color: #586069; font-size: 0.9rem;">Active</div>
    </div>
    <div class="card" style="text-align: center;">
        <div style="font-size: 2rem; font-weight: 700; color: #735c0f;"><?= $stats['pending'] ?? 0 ?></div>
        <div style="color: #586069; font-size: 0.9rem;">Pending Approval</div>
    </div>
    <div class="card" style="text-align: center;">
        <div style="font-size: 2rem; font-weight: 700; color: #d73a49;"><?= count($lowStockProducts) ?></div>
        <div style="color: #586069; font-size: 0.9rem;">Low Stock Alerts</div>
    </div>
</div>

<!-- Recent Products -->
<div class="card">
    <h2 style="margin-bottom: 1rem;">Your Recent Products</h2>
    <?php if (empty($recentProducts)): ?>
        <p style="color: #586069;">No products yet. <a href="/products/create" style="color: #0366d6;">Add your first product</a>.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentProducts as $product): ?>
                <tr>
                    <td><code><?= htmlspecialchars($product['sku']) ?></code></td>
                    <td><a href="/products/<?= $product['id'] ?>" style="color: #0366d6; text-decoration: none;"><?= htmlspecialchars($product['name']) ?></a></td>
                    <td><?= htmlspecialchars($product['category']) ?></td>
                    <td>$<?= number_format($product['unit_price'], 2) ?></td>
                    <td>
                        <?php if ($product['quantity_available'] <= 10): ?>
                            <span class="badge badge-low"><?= $product['quantity_available'] ?></span>
                        <?php else: ?>
                            <?= $product['quantity_available'] ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Low Stock Alerts -->
<?php if (!empty($lowStockProducts)): ?>
<div class="card">
    <h2 style="margin-bottom: 1rem; color: #d73a49;">⚠️ Low Stock Alerts</h2>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>SKU</th>
                <th>Current Stock</th>
                <th>Min Order</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (array_slice($lowStockProducts, 0, 5) as $product): ?>
            <tr>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><code><?= htmlspecialchars($product['sku']) ?></code></td>
                <td><span class="badge badge-low"><?= $product['quantity_available'] ?></span></td>
                <td><?= $product['minimum_order'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if (count($lowStockProducts) > 5): ?>
        <p style="margin-top: 0.75rem;"><a href="/products/low-stock" style="color: #0366d6;">View all <?= count($lowStockProducts) ?> low stock items →</a></p>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
