<?php
session_start();
include 'includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'cart' => []]);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "
SELECT p.id, p.name, p.price, p.image_url_1, uc.quantity
FROM user_cart uc
JOIN products p ON p.id = uc.product_id
WHERE uc.user_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart = [];
while ($row = $result->fetch_assoc()) {
    $cart[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'price' => (float) $row['price'],
        'quantity' => (int) $row['quantity'],
        'image' => $row['image_url_1'] ?? 'images/no-image.png'
    ];
}

echo json_encode(['success' => true, 'cart' => $cart]);
