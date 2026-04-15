<?php $pageTitle = 'Login'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - OctoCAT Supplier Portal</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .login-card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.12); width: 100%; max-width: 400px; }
        .login-card h1 { font-size: 1.5rem; margin-bottom: 0.5rem; }
        .login-card p { color: #586069; margin-bottom: 1.5rem; font-size: 0.9rem; }
        .alert-error { background: #ffeef0; color: #d73a49; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem; border: 1px solid #d73a49; font-size: 0.9rem; }
        .alert-success { background: #dcffe4; color: #22863a; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem; border: 1px solid #34d058; font-size: 0.9rem; }
        label { display: block; font-weight: 500; font-size: 0.9rem; margin-bottom: 0.25rem; }
        input { width: 100%; padding: 0.5rem; border: 1px solid #d1d5da; border-radius: 6px; margin-bottom: 1rem; font-size: 0.9rem; }
        .btn { width: 100%; padding: 0.75rem; background: #2ea44f; color: white; border: none; border-radius: 6px; font-size: 1rem; cursor: pointer; }
        .btn:hover { background: #2c974b; }
        .footer-link { text-align: center; margin-top: 1rem; font-size: 0.85rem; }
        .footer-link a { color: #0366d6; text-decoration: none; }
    </style>
</head>
<body>
    <div class="login-card">
        <h1>🐱 Supplier Portal</h1>
        <p>Sign in to manage your product catalog</p>

        <?php if (!empty($error)): ?>
            <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['registered'])): ?>
            <div class="alert-success">Registration successful! Please log in.</div>
        <?php endif; ?>

        <form method="POST" action="/login">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autocomplete="email"
                   value="<?= htmlspecialchars($email ?? '') ?>">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">

            <button type="submit" class="btn">Sign In</button>
        </form>

        <div class="footer-link">
            <a href="/register">Create a supplier account</a>
        </div>
    </div>
</body>
</html>
