<?php
$pageTitle = 'Registration';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - OctoCAT Supplier Portal</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 2rem 0; }
        .register-card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.12); width: 100%; max-width: 500px; }
        .register-card h1 { font-size: 1.5rem; margin-bottom: 0.5rem; }
        .register-card p { color: #586069; margin-bottom: 1.5rem; font-size: 0.9rem; }
        .alert-error { background: #ffeef0; color: #d73a49; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem; border: 1px solid #d73a49; font-size: 0.9rem; }
        label { display: block; font-weight: 500; font-size: 0.9rem; margin-bottom: 0.25rem; }
        input { width: 100%; padding: 0.5rem; border: 1px solid #d1d5da; border-radius: 6px; margin-bottom: 1rem; font-size: 0.9rem; }
        .btn { width: 100%; padding: 0.75rem; background: #2ea44f; color: white; border: none; border-radius: 6px; font-size: 1rem; cursor: pointer; }
        .btn:hover { background: #2c974b; }
        .footer-link { text-align: center; margin-top: 1rem; font-size: 0.85rem; }
        .footer-link a { color: #0366d6; text-decoration: none; }
        .row { display: flex; gap: 1rem; }
        .row > div { flex: 1; }
    </style>
</head>
<body>
    <div class="register-card">
        <h1>🐱 Supplier Registration</h1>
        <p>Create an account to manage your product catalog in the OctoCAT Supply Chain</p>

        <?php if (!empty($errors)): ?>
            <div class="alert-error">
                <ul style="margin: 0; padding-left: 1.2rem;">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="/register">
            <label for="company_name">Company Name *</label>
            <input type="text" id="company_name" name="company_name" required
                   value="<?= htmlspecialchars($data['company_name'] ?? '') ?>">

            <label for="contact_name">Contact Name *</label>
            <input type="text" id="contact_name" name="contact_name" required
                   value="<?= htmlspecialchars($data['contact_name'] ?? '') ?>">

            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email" required
                   value="<?= htmlspecialchars($data['email'] ?? '') ?>">

            <div class="row">
                <div>
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone"
                           value="<?= htmlspecialchars($data['phone'] ?? '') ?>">
                </div>
                <div>
                    <label for="password">Password * (min 8 chars)</label>
                    <input type="password" id="password" name="password" required minlength="8">
                </div>
            </div>

            <label for="address">Business Address</label>
            <input type="text" id="address" name="address"
                   value="<?= htmlspecialchars($data['address'] ?? '') ?>">

            <button type="submit" class="btn">Create Account</button>
        </form>

        <div class="footer-link">
            Already have an account? <a href="/login">Sign in</a>
        </div>
    </div>
</body>
</html>
