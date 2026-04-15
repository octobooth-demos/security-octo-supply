<?php

return [
    'driver' => 'sqlite',
    'database' => getenv('DB_DATABASE') ?: __DIR__ . '/../storage/database/supplier_portal.sqlite',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];
