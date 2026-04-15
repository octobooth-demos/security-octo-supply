<?php
$pageTitle = 'Products';
ob_start();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h1>Product Catalog</h1>
    <div>
        <a href="/products/create" class="btn btn-primary">+ Add Product</a>
        <a href="/products/low-stock" class="btn btn-secondary" style="margin-left: 0.5rem;">Low Stock Alert</a>
    </div>
</div>

<!-- Search and Filter -->
<div class="card" style="padding: 1rem;">
    <form method="GET" action="/products" style="display: flex; gap: 1rem; align-items: end;">
        <div style="flex: 1;">
            <label for="search">Search</label>
            <input type="text" id="search" name="search" placeholder="Search by name, SKU, or description..."
                   value="<?= htmlspecialchars($search ?? '') ?>" style="margin-bottom: 0;">
        </div>
        <div>
            <label for="category">Category</label>
            <select id="category" name="category" style="margin-bottom: 0;">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['category']) ?>"
                            <?= ($category ?? '') === $cat['category'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['category']) ?> (<?= $cat['product_count'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-bottom: 0;">Filter</button>
    </form>
</div>

<!-- Product Table -->
<div class="card">
    <?php if (empty($products)): ?>
        <p style="text-align: center; color: #586069; padding: 2rem;">
            No products found. <a href="/products/create">Add your first product</a>.
        </p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Unit Price</th>
                    <th>Stock</th>
                    <th>Min Order</th>
                    <th>Lead Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><code><?= htmlspecialchars($product['sku']) ?></code></td>
                    <td>
                        <a href="/products/<?= $product['id'] ?>" style="color: #0366d6; text-decoration: none;">
                            <?= htmlspecialchars($product['name']) ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($product['category']) ?></td>
                    <td>$<?= number_format($product['unit_price'], 2) ?></td>
                    <td>
                        <?php if ($product['quantity_available'] <= 10): ?>
                            <span class="badge badge-low"><?= $product['quantity_available'] ?></span>
                        <?php else: ?>
                            <?= $product['quantity_available'] ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $product['minimum_order'] ?></td>
                    <td><?= $product['lead_time_days'] ?> days</td>
                    <td>
                        <a href="/products/<?= $product['id'] ?>/edit" style="color: #0366d6; font-size: 0.85rem;">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Pagination -->
<?php if (!empty($products) && count($products) >= 25): ?>
<div style="text-align: center; margin-top: 1rem;">
    <?php if ($page > 1): ?>
        <a href="/products?page=<?= $page - 1 ?>" class="btn btn-secondary">← Previous</a>
    <?php endif; ?>
    <span style="margin: 0 1rem; color: #586069;">Page <?= $page ?></span>
    <a href="/products?page=<?= $page + 1 ?>" class="btn btn-secondary">Next →</a>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
