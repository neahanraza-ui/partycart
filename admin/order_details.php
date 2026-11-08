<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

include('db.php');

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch order details
$order_query = "SELECT * FROM orders WHERE id = ?";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo "<script>alert('Order not found!'); window.location.href='orders.php';</script>";
    exit();
}

// Fetch order items with product details
$items_query = "SELECT oi.*, p.name as product_name 
                FROM order_items oi 
                LEFT JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ?";
$stmt = $conn->prepare($items_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - LUXORA Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <?php include('sidebar.php'); ?>

    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Order #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></h1>
            <a href="orders.php" class="btn" style="background: transparent; border: 1px solid var(--gold-primary); color: var(--gold-primary);">Back to Orders</a>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div style="background: #161616; padding: 20px; border-radius: 8px; border: 1px solid #333;">
                <h3 style="color: var(--gold-primary); margin-top: 0;">Customer Information</h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($order['user_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['user_email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['user_phone']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($order['user_address']); ?></p>
            </div>

            <div style="background: #161616; padding: 20px; border-radius: 8px; border: 1px solid #333;">
                <h3 style="color: var(--gold-primary); margin-top: 0;">Order Information</h3>
                <p><strong>Order Date:</strong> <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></p>
                <p><strong>Status:</strong> <span style="text-transform: capitalize;"><?php echo htmlspecialchars($order['status']); ?></span></p>
                <p><strong>Total Amount:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
            </div>
        </div>

        <div class="table-container">
            <h3 style="color: var(--gold-primary); margin-bottom: 15px;">Order Items</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Color</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $items->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo !empty($item['color']) ? htmlspecialchars($item['color']) : '-'; ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
