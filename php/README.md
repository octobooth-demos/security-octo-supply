# OctoCAT Supplier Portal (PHP)

A legacy PHP microservice that provides a self-service portal for suppliers in the OctoCAT Supply Chain ecosystem. Suppliers use this portal to manage their product catalogs, update stock levels, and export reports.

## Architecture

This service complements the main Node.js/Express API by providing a dedicated supplier-facing interface built on PHP 8.1+ with SQLite persistence.

```
php/
├── public/          → Web root (front controller + .htaccess)
├── config/          → Application and database configuration
├── src/
│   ├── Auth/        → Session-based authentication
│   ├── Controllers/ → Request handlers (Auth, Product, Supplier, Report)
│   ├── Models/      → Data access layer (PDO/SQLite)
│   └── Middleware/  → Auth guard middleware
├── templates/       → PHP view templates
├── migrations/      → SQLite schema migrations
└── composer.json    → Dependencies (monolog, phpdotenv)
```

## Running Locally

```bash
# Install dependencies
composer install

# Run database migrations
sqlite3 storage/database/supplier_portal.sqlite < migrations/001_create_tables.sql

# Start development server
php -S localhost:8080 -t public/
```

## Features

- **Authentication**: Session-based login/registration with bcrypt password hashing
- **Product Management**: Full CRUD for supplier product catalogs
- **Reporting**: CSV/JSON export, inventory summaries, price analysis
- **Stock Alerts**: Low-stock monitoring with configurable thresholds
- **API Endpoints**: JSON API for integration with the main supply chain system

## Tech Stack

- PHP 8.1+
- SQLite via PDO
- Monolog for logging
- PHPUnit for testing
- No framework (lightweight custom routing)
