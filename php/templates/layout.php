<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Supplier Portal') ?> - OctoCAT Supply Chain</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; color: #333; line-height: 1.6; }
        .navbar { background: #24292e; color: white; padding: 0.75rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: #fff; text-decoration: none; margin-left: 1.5rem; font-size: 0.9rem; }
        .navbar a:hover { color: #58a6ff; }
        .navbar .brand { font-weight: 600; font-size: 1.1rem; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.12); padding: 1.5rem; margin-bottom: 1.5rem; }
        .btn { display: inline-block; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-size: 0.9rem; cursor: pointer; border: 1px solid transparent; }
        .btn-primary { background: #2ea44f; color: white; }
        .btn-primary:hover { background: #2c974b; }
        .btn-danger { background: #d73a49; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #e1e4e8; }
        th { background: #f6f8fa; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; }
        .alert { padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1rem; }
        .alert-success { background: #dcffe4; color: #22863a; border: 1px solid #34d058; }
        .alert-error { background: #ffeef0; color: #d73a49; border: 1px solid #d73a49; }
        input, select, textarea { width: 100%; padding: 0.5rem; border: 1px solid #d1d5da; border-radius: 6px; font-size: 0.9rem; margin-bottom: 0.75rem; }
        label { font-weight: 500; font-size: 0.9rem; display: block; margin-bottom: 0.25rem; }
        .badge { padding: 0.2rem 0.6rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600; }
        .badge-active { background: #dcffe4; color: #22863a; }
        .badge-pending { background: #fff5b1; color: #735c0f; }
        .badge-low { background: #ffeef0; color: #d73a49; }
    </style>
</head>
<body>
    <?php if (isset($_SESSION['authenticated']) && $_SESSION['authenticated']): ?>
    <nav class="navbar">
        <span class="brand">🐱 OctoCAT Supplier Portal</span>
        <div>
            <a href="/dashboard">Dashboard</a>
            <a href="/products">Products</a>
            <a href="/reports">Reports</a>
            <a href="/profile">Profile</a>
            <a href="/logout">Logout</a>
        </div>
    </nav>
    <?php endif; ?>

    <div class="container">
        <?= $content ?? '' ?>
    </div>
</body>
</html>
