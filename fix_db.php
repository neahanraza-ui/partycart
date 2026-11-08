<?php
include 'includes/db.php';

echo "<h2>Database Synchronization Tool</h2>";

// Function to add column if it doesn't exist
function addColumn($conn, $table, $column, $type) {
    $check = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    if ($check->num_rows == 0) {
        $sql = "ALTER TABLE `$table` ADD `$column` $type";
        if ($conn->query($sql)) {
            echo "Added column '$column' to table '$table'.<br>";
        } else {
            echo "Error adding column '$column': " . $conn->error . "<br>";
        }
    } else {
        echo "Column '$column' already exists in table '$table'.<br>";
    }
}

// Ensure Orders Table is correct
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

if ($conn->query($sql_orders)) {
    echo "Table 'orders' ensured.<br>";
} else {
    echo "Error with 'orders' table: " . $conn->error . "<br>";
}

// Add missing columns to orders if CREATE TABLE didn't update it
addColumn($conn, 'orders', 'user_name', "VARCHAR(100) NOT NULL AFTER user_id");
addColumn($conn, 'orders', 'user_email', "VARCHAR(100) NOT NULL AFTER user_name");
addColumn($conn, 'orders', 'user_phone', "VARCHAR(20) NOT NULL AFTER user_email");
addColumn($conn, 'orders', 'user_address', "TEXT NOT NULL AFTER user_phone");
addColumn($conn, 'orders', 'total_amount', "DECIMAL(10, 2) NOT NULL AFTER user_address");

// Ensure Order Items Table is correct
$sql_order_items = "CREATE TABLE IF NOT EXISTS order_items (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    order_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    quantity INT(11) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
)";

if ($conn->query($sql_order_items)) {
    echo "Table 'order_items' ensured.<br>";
} else {
    echo "Error with 'order_items' table: " . $conn->error . "<br>";
}

// Ensure game_log table exists
$sql_game_log = "CREATE TABLE IF NOT EXISTS game_log (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    difficulty VARCHAR(20) NOT NULL,
    points INT(11) DEFAULT 0,
    played_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($sql_game_log)) {
    echo "Table 'game_log' ensured.<br>";
}

// Update Coupons Table
addColumn($conn, 'coupons', 'email', "VARCHAR(100) DEFAULT NULL AFTER user_id");
addColumn($conn, 'coupons', 'max_uses', "INT(11) DEFAULT 1 AFTER discount");
addColumn($conn, 'coupons', 'used_count', "INT(11) DEFAULT 0 AFTER max_uses");
addColumn($conn, 'coupons', 'difficulty', "VARCHAR(20) DEFAULT NULL AFTER used_count");

// Ensure user_cart table exists
$sql_user_cart = "CREATE TABLE IF NOT EXISTS user_cart (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    quantity INT(11) NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
)";
if ($conn->query($sql_user_cart)) {
    echo "Table 'user_cart' ensured with UNIQUE constraint.<br>";
}

// Update user_id in orders to be nullable if it's not
$conn->query("ALTER TABLE orders MODIFY user_id INT(11) DEFAULT NULL");

echo "<br><b>Database fix complete. Please try your order again.</b>";
?>
