<?php
session_start();
include 'includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['cart'])) {
    echo json_encode(['success' => false]);
    exit;
}

foreach ($data['cart'] as $item) {
    $sql = "
        INSERT INTO user_cart (user_id, product_id, quantity)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $user_id, $item['id'], $item['quantity']);
    $stmt->execute();
}

/* Clear session cart after merge */
unset($_SESSION['cart']);

echo json_encode(['success' => true]);
?>
<script>
    (async () => {
        const cart = JSON.parse(localStorage.getItem('guest_cart'));
        if (!cart) return;

        await fetch('sync_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ cart })
        });

        localStorage.removeItem('guest_cart');
    })();
</script>