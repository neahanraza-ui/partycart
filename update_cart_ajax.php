<?php
session_start();
include 'includes/db.php';

/* SUPPORT JSON + FORM */
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    $data = $_POST;
}

$id = (int) ($data['id'] ?? 0);
$action = $data['action'] ?? '';

if ($id <= 0 || !in_array($action, ['increase', 'decrease', 'remove'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
    exit;
}

/* =========================
   LOGGED-IN USER → DB CART
   ========================= */
if (isset($_SESSION['user_id'])) {

    $uid = (int) $_SESSION['user_id'];

    if ($action === 'increase') {
        $sql = "UPDATE user_cart 
                SET quantity = quantity + 1 
                WHERE user_id = ? AND product_id = ?";
    } elseif ($action === 'decrease') {
        $sql = "UPDATE user_cart 
                SET quantity = quantity - 1 
                WHERE user_id = ? AND product_id = ?";
    } elseif ($action === 'remove') {
        $sql = "DELETE FROM user_cart 
                WHERE user_id = ? AND product_id = ?";
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $uid, $id);
    $stmt->execute();

    // cleanup zero quantity
    $conn->query("DELETE FROM user_cart WHERE quantity <= 0");

    echo json_encode(['success' => true]);
    exit;
}

/* =========================
   GUEST → SESSION CART
   ========================= */
if (empty($_SESSION['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Cart empty']);
    exit;
}

foreach ($_SESSION['cart'] as $key => &$item) {

    if ((int) $item['id'] === $id) {

        if ($action === 'increase') {
            $item['quantity']++;
        } elseif ($action === 'decrease') {
            $item['quantity']--;
            if ($item['quantity'] <= 0) {
                unset($_SESSION['cart'][$key]);
            }
        } elseif ($action === 'remove') {
            unset($_SESSION['cart'][$key]);
        }

        $_SESSION['cart'] = array_values($_SESSION['cart']); // reindex
        echo json_encode(['success' => true]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Item not found']);
?>
