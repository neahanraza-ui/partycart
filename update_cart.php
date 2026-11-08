<?php
session_start();

$id = $_POST['id'] ?? null;
$action = $_POST['action'] ?? null;

if (!$id || !$action || empty($_SESSION['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

foreach ($_SESSION['cart'] as $key => &$item) {

    if ((int) $item['id'] === (int) $id) {

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
