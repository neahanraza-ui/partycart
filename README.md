# PartyCart

PartyCart is a PHP and MySQL e-commerce application branded as **LUXORA**. It includes a customer storefront for browsing products and collections, managing a shopping cart, applying coupons, and placing orders, along with customer accounts and an administrative area.

## Features

- Product catalog, product details, and featured collections
- Session-backed shopping cart and cart synchronization
- Checkout, order processing, and order confirmation
- Customer registration, sign-in, and profile pages
- Admin tools for products, categories, customers, orders, store content, and metadata
- A browser maze game in `game/Maze-Game-main`

## Requirements

- PHP with the `mysqli` extension
- MySQL or MariaDB
- Apache or another PHP-capable web server

## Setup

1. Create a database named `luxora_db`.
2. Configure the database host, username, password, and database name in `includes/db.php` and `admin/db.php`.
3. Prepare the required database schema and seed data. No complete SQL dump is included. `setup_db.php` and `setup_meta_db.php` create only selected tables and do not create the full schema used by the application.
4. From the project root, run `php -S localhost:8000` and open `http://localhost:8000/index.php`.

The included database configuration uses local development defaults. Replace those credentials and review the session/security settings before deploying.