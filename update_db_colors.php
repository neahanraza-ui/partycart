<?php
include 'includes/db.php';

function addColumnIfNotExists($conn, $table, $column, $definition) {
    $check = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    if ($check->num_rows == 0) {
        $sql = "ALTER TABLE `$table` ADD COLUMN `$column` $definition";
        if ($conn->query($sql) === TRUE) {
            echo "Added column '$column' to table '$table'.<br>";
        } else {
            echo "Error adding column '$column' to table '$table': " . $conn->error . "<br>";
        }
    } else {
        echo "Column '$column' already exists in table '$table'.<br>";
    }
}

// 1. Add color to products (available colors, comma-separated)
addColumnIfNotExists($conn, 'products', 'color', "VARCHAR(255) DEFAULT '' AFTER price");

// 2. Add color to user_cart (selected color)
addColumnIfNotExists($conn, 'user_cart', 'color', "VARCHAR(50) DEFAULT '' AFTER quantity");

// 3. Add color to order_items (selected color)
addColumnIfNotExists($conn, 'order_items', 'color', "VARCHAR(50) DEFAULT '' AFTER price");

echo "Database schema update complete.";
?>
