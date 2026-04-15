<?php

return [
    'name' => 'OctoCAT Supplier Portal',
    'version' => '1.4.2',
    'environment' => getenv('APP_ENV') ?: 'production',
    'debug' => filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOLEAN),
    'timezone' => 'UTC',
    'upload_dir' => __DIR__ . '/../storage/uploads',
    'max_upload_size' => 10 * 1024 * 1024, // 10MB
    'session' => [
        'lifetime' => 3600,
        'name' => 'supplier_session',
        'secure' => true,
        'httponly' => true,
    ],
    'log' => [
        'path' => __DIR__ . '/../storage/logs/app.log',
        'level' => getenv('LOG_LEVEL') ?: 'warning',
    ],
    'cors' => [
        'allowed_origins' => explode(',', getenv('CORS_ORIGINS') ?: 'http://localhost:3000'),
    ],
];
