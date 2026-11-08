<?php
session_start();
include 'includes/db.php';

header('Content-Type: application/json');

if (!isset($_POST['product_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$product_id = (int) $_POST['product_id'];
$color = isset($_POST['product_color']) ? trim($_POST['product_color']) : '';

/* FETCH PRODUCT FROM DB (SECURITY) */
$stmt = $conn->prepare("SELECT id, name, price, image_url_1 FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo json_encode(['success' => false]);
    exit;
}

/* SESSION CART (GUEST + LOGGED-IN) */
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$found = false;
foreach ($_SESSION['cart'] as &$item) {
    // Match by both product ID and color
    if ($item['id'] == $product_id && $item['color'] == $color) {
        $item['quantity']++;
        $found = true;
        break;
    }
}

if (!$found) {
    $_SESSION['cart'][] = [
        'id' => $product['id'],
        'name' => $product['name'],
        'price' => (float) $product['price'],
        'image' => $product['image_url_1'] ?? 'images/no-image.png',
        'color' => $color,
        'quantity' => 1
    ];
}

/* DB CART (ONLY IF LOGGED IN) */
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];

    // Check if this product+color combo exists
    $check_stmt = $conn->prepare("SELECT id, quantity FROM user_cart WHERE user_id = ? AND product_id = ? AND color = ?");
    $check_stmt->bind_param("iis", $uid, $product_id, $color);
    $check_stmt->execute();
    $existing = $check_stmt->get_result()->fetch_assoc();

    if ($existing) {
        // Update quantity
        $update_stmt = $conn->prepare("UPDATE user_cart SET quantity = quantity + 1 WHERE id = ?");
        $update_stmt->bind_param("i", $existing['id']);
        $update_stmt->execute();
    } else {
        // Insert new entry
        $insert_stmt = $conn->prepare("INSERT INTO user_cart (user_id, product_id, color, quantity) VALUES (?, ?, ?, 1)");
        $insert_stmt->bind_param("iis", $uid, $product_id, $color);
        $insert_stmt->execute();
    }
}

echo json_encode([
    'success' => true,
    'cart_count' => array_sum(array_column($_SESSION['cart'], 'quantity'))
]);
