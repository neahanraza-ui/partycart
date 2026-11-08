# PartyCart

PartyCart is a PHP and MySQL e-commerce application branded as **LUXORA**. It includes a customer storefront for browsing products and collections, managing a shopping cart, applying coupons, and placing orders, along with customer accounts and an administrative area.

## Features

- Product catalog, product details, and featured collections
- Session-backed shopping cart and cart synchronization
- Checkout, order processing, and order confirmation
- Customer registration, sign-in, and profile pages

## Admin Panel

The admin panel is available at `/admin/login.php` and requires an existing admin account. Its dashboard summarizes order, product, and category counts. Admins can add and edit products and categories, review orders and update their status, read contact messages, manage customer and admin accounts, edit About Us content, and update page metadata.

## Game

The browser maze game is in `game/Maze-Game-main/`. After starting the local server, open `http://localhost:8000/game/Maze-Game-main/` to play. Players choose one of four difficulty levels and receive a reward offer after completing the maze.

### Maze Coupons

After completing the maze, a player can submit an email address to claim a coupon. The code is displayed in the game and can be entered at checkout; it is bound to the claiming email. The game allows one reward claim per email. Reward tiers shown by the game are:

| Difficulty | Discount | Uses |
| --- | ---: | ---: |
| Easy | 5% | 1 |
| Medium | 7% | 1 |
| Hard | 15% | 2 |
| Extreme | 20% | 2 |

Coupon generation also records the email and difficulty in `game_log`. The database must include the `game_log` and coupon fields expected by the game; `fix_db.php` contains the related schema updates.

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