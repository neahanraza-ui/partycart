<?php
include 'includes/db.php';

// Create Users Table
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_users) === TRUE) {
    echo "Table 'users' created successfully or already exists.<br>";
} else {
    echo "Error creating table 'users': " . $conn->error . "<br>";
}

// Create Orders Table (Updated for guest checkout and contact details)
$sql_orders = "CREATE TABLE IF NOT EXISTS orders (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) DEFAULT NULL,
    user_name VARCHAR(100) NOT NULL,
    user_email VARCHAR(100) NOT NULL,
    user_phone VARCHAR(20) NOT NULL,
    user_address TEXT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
)";

if ($conn->query($sql_orders) === TRUE) {
    echo "Table 'orders' created/updated successfully.<br>";
} else {
    echo "Error creating table 'orders': " . $conn->error . "<br>";
}

// Create Order Items Table
$sql_order_items = "CREATE TABLE IF NOT EXISTS order_items (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    order_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    quantity INT(11) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
)";

if ($conn->query($sql_order_items) === TRUE) {
    echo "Table 'order_items' created successfully.<br>";
} else {
    echo "Error creating table 'order_items': " . $conn->error . "<br>";
}

// Create Coupons Table
$sql_coupons = "CREATE TABLE IF NOT EXISTS coupons (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    discount DECIMAL(10, 2) NOT NULL,
    max_uses INT(11) DEFAULT 1,
    used_count INT(11) DEFAULT 0,
    difficulty VARCHAR(20) DEFAULT NULL,
    status VARCHAR(20) DEFAULT 'Active',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
)";

if ($conn->query($sql_coupons) === TRUE) {
    echo "Table 'coupons' created successfully or already exists.<br>";
} else {
    echo "Error creating table 'coupons': " . $conn->error . "<br>";
}

$conn->close();
?>
