<?php
include('db.php');
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

// Database connection logic already handled by db.php
include('sidebar.php');

// Fetch stats (orders, products, categories, etc.)
$order_count_query = "SELECT COUNT(*) AS total_orders FROM orders";
$order_count_result = mysqli_query($conn, $order_count_query);
$order_count = mysqli_fetch_assoc($order_count_result)['total_orders'];

$product_count_query = "SELECT COUNT(*) AS total_products FROM products";
$product_count_result = mysqli_query($conn, $product_count_query);
$product_count = mysqli_fetch_assoc($product_count_result)['total_products'];

$category_count_query = "SELECT COUNT(*) AS total_categories FROM categories";
$category_count_result = mysqli_query($conn, $category_count_query);
$category_count = mysqli_fetch_assoc($category_count_result)['total_categories'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LUXORA</title>
    <link rel="stylesheet" href="admin.css"> <!-- Link to new CSS -->
</head>

<body>
    

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Dashboard Overview</h1>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Orders</h3>
                <div class="value"><?php echo $order_count; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Products</h3>
                <div class="value"><?php echo $product_count; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Categories</h3>
                <div class="value"><?php echo $category_count; ?></div>
            </div>
        </div>

        <div class="table-container">
            <h2 style="color: var(--gold-primary); margin-bottom: 20px; font-family: var(--font-heading);">Recent Activity</h2>
            <!-- You could add a recent orders table here -->
            <p style="color: var(--text-secondary);">Welcome to the Luxora Admin Panel. Use the sidebar to manage your store.</p>
        </div>
    </div>

</body>

</html>