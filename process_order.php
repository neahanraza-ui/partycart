<?php
// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/checkout_error.log');

// Always return JSON
header('Content-Type: application/json; charset=utf-8');

// Database connection (and session_start inside)
include 'includes/db.php';

// Read + decode JSON body
$rawData = file_get_contents("php://input");
$orderData = json_decode($rawData, true);

// Basic JSON validation
if (!is_array($orderData)) {
    error_log("Invalid JSON received: " . substr($rawData ?? '', 0, 500));
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON request']);
    exit;
}

// Extract fields safely
$userName = trim($orderData['userName'] ?? '');
$userEmail = trim($orderData['userEmail'] ?? '');
$userPhone = trim($orderData['userPhone'] ?? '');
$userAddress = trim($orderData['userAddress'] ?? '');
$cartData = $orderData['cartData'] ?? [];
$orderTotal = $orderData['orderTotal'] ?? 0;
$couponCode = isset($orderData['couponCode']) && $orderData['couponCode'] !== '' ? trim($orderData['couponCode']) : null;

// Session user id (can be null)
$userId = isset($_SESSION['user_id']) && $_SESSION['user_id'] !== '' ? (int) $_SESSION['user_id'] : null;

// Validate required fields
if ($userName === '' || $userEmail === '' || $userAddress === '' || !is_array($cartData) || count($cartData) === 0) {
    error_log("Invalid order data received: " . substr($rawData ?? '', 0, 500));
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Missing required fields or cart is empty.']);
    exit;
}

// Validate cart items (avoid undefined index issues)
foreach ($cartData as $i => $item) {
    if (!is_array($item) || !isset($item['id'], $item['quantity'], $item['price'])) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => "Invalid cart item at index {$i}."]);
        exit;
    }
    // Normalize types
    $cartData[$i]['id'] = (int) $item['id'];
    $cartData[$i]['quantity'] = (int) $item['quantity'];
    $cartData[$i]['price'] = (float) $item['price'];
}

// Normalize total
$orderTotal = (float) $orderTotal;

// Enable mysqli exceptions
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Begin transaction
    $conn->begin_transaction();

    // --- COUPON VALIDATION ---
    if ($couponCode) {
        $stmt_coupon = $conn->prepare("
            SELECT discount, max_uses, used_count, email
            FROM coupons
            WHERE code = ? AND status = 'Active'
            FOR UPDATE
        ");
        $stmt_coupon->bind_param("s", $couponCode);
        $stmt_coupon->execute();
        $res_coupon = $stmt_coupon->get_result();

        if ($res_coupon->num_rows === 0) {
            throw new Exception("Invalid coupon.");
        }

        $coupon = $res_coupon->fetch_assoc();

        // Validate email binding (case-insensitive match)
        if (!empty($coupon['email']) && strcasecmp(trim($coupon['email']), $userEmail) !== 0) {
            throw new Exception("This coupon is bound to another email.");
        }

        // Validate usage limit
        if ((int) $coupon['used_count'] >= (int) $coupon['max_uses']) {
            throw new Exception("Coupon limit reached.");
        }

        // Update coupon usage
        $stmt_update_coupon = $conn->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE code = ?");
        $stmt_update_coupon->bind_param("s", $couponCode);
        $stmt_update_coupon->execute();
    }

    // --- INSERT ORDER ---
    // Handle nullable user_id safely by using a different INSERT if not logged in
    if ($userId !== null) {
        $stmt = $conn->prepare("
            INSERT INTO orders (user_id, user_name, user_email, user_phone, user_address, total_amount)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("issssd", $userId, $userName, $userEmail, $userPhone, $userAddress, $orderTotal);
    } else {
        $stmt = $conn->prepare("
            INSERT INTO orders (user_name, user_email, user_phone, user_address, total_amount)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssd", $userName, $userEmail, $userPhone, $userAddress, $orderTotal);
    }

    $stmt->execute();
    $orderId = $stmt->insert_id;

    // --- INSERT ORDER ITEMS ---
    $stmt_item = $conn->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price, color)
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($cartData as $item) {
        $pid = (int) $item['id'];
        $qty = (int) $item['quantity'];
        $price = (float) $item['price'];
        $color = isset($item['color']) ? trim($item['color']) : '';

        $stmt_item->bind_param("iiids", $orderId, $pid, $qty, $price, $color);
        $stmt_item->execute();
    }

    // Commit
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully',
        'orderId' => $orderId
    ]);
} catch (Exception $e) {
    // Rollback on failure
    if (isset($conn) && $conn instanceof mysqli) {
        try {
            $conn->rollback();
        } catch (Throwable $t) {
        }
    }

    error_log("Order Processing Error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
} finally {
    // Close DB
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>