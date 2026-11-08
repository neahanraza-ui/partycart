<?php
include 'includes/db.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$order = null;
$order_items = [];

if ($order_id > 0) {
    // Fetch order details
    $sql = "SELECT * FROM orders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();

        // Fetch order items
        $sql_items = "SELECT oi.*, p.name, p.description FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?";
        $stmt_items = $conn->prepare($sql_items);
        $stmt_items->bind_param("i", $order_id);
        $stmt_items->execute();
        $result_items = $stmt_items->get_result();
        while ($row = $result_items->fetch_assoc()) {
            $order_items[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - LUXORA</title>
    <link rel="stylesheet" href="styles/pages.css">
    <link rel="stylesheet" href="styles/fonts.css">
    <link rel="stylesheet" href="styles/luxury-theme.css">
    <link rel="stylesheet" href="styles/tailwind.css">
    <link rel="stylesheet" href="styles/theme.css">
    <style>
        .thank-you-container {
            text-align: center;
            margin-top: 100px;
            padding: 20px;
        }

        .receipt-container {
            max-width: 800px;
            margin: 40px auto;
            background-color: #1a1a1a;
            padding: 30px;
            border: 1px solid var(--gold-primary);
            border-radius: 8px;
            text-align: left;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #333;
            padding-bottom: 20px;
        }

        .receipt-header h2 {
            color: var(--gold-primary);
            font-family: 'Playfair Display', serif;
        }

        .order-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            color: #ccc;
        }

        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .receipt-table th,
        .receipt-table td {
            padding: 12px;
            border-bottom: 1px solid #333;
            text-align: left;
            color: #ddd;
        }

        .receipt-table th {
            color: var(--gold-primary);
            font-weight: bold;
        }

        .receipt-total {
            text-align: right;
            font-size: 24px;
            color: var(--gold-primary);
            margin-top: 20px;
            font-weight: bold;
        }

        .btn-luxury {
            background-color: var(--gold-primary);
            color: #000;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        
        .btn-luxury:hover {
            background-color: #d4af37;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <?php include 'includes/header.php'; ?>


    <!-- Thank You Page -->
    <section class="thank-you-container">
        <h1 style="color: var(--gold-primary); font-family: 'Playfair Display'; font-size: 48px;">Order Confirmed!</h1>
        <p style="color: #ccc; font-size: 18px;">Thank you for your purchase. Your order has been placed successfully.</p>

        <?php if ($order): ?>
            <div class="receipt-container">
                <div class="receipt-header">
                    <h2>Receipt / Voucher</h2>
                    <p>Order #<?php echo str_pad($order_id, 8, '0', STR_PAD_LEFT); ?></p>
                </div>

                <div class="order-info">
                    <div>
                        <strong>Customer:</strong> <?php echo htmlspecialchars($order['user_name']); ?><br>
                        <strong>Email:</strong> <?php echo htmlspecialchars($order['user_email']); ?>
                    </div>
                    <div style="text-align: right;">
                        <strong>Date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?><br>
                        <strong>Status:</strong> <?php echo ucfirst($order['status']); ?>
                    </div>
                </div>

                <table class="receipt-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Color</th>
                            <th>Description</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo !empty($item['color']) ? htmlspecialchars($item['color']) : '-'; ?></td>
                                <td style="font-size: 14px; color: #888;"><?php echo htmlspecialchars(substr($item['description'], 0, 50)) . '...'; ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="receipt-total">
                    Total: $<?php echo number_format($order['total_amount'], 2); ?>
                </div>
                
                <div style="text-align: center; margin-top: 30px;">
                    <a href="javascript:window.print()" style="color: #888; text-decoration: underline; margin-right: 20px;">Print Receipt</a>
                </div>
            </div>
        <?php else: ?>
            <p style="color: red; margin: 30px;">Order details not found.</p>
        <?php endif; ?>

        <div style="margin-top: 30px;">
            <a href="products.php" class="btn-luxury">Continue Shopping</a>
            <a href="index.php" class="btn-luxury" style="background: transparent; border: 1px solid var(--gold-primary); color: var(--gold-primary);">Back to Home</a>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

</body>

</html>