<?php
session_start();
include 'includes/db.php';

$cart_items = [];
$total_price = 0;
$count = 0;

/* =========================
   LOGGED-IN USER → DB CART
   ========================= */
if (!empty($_SESSION['user_id'])) {

    $uid = (int) $_SESSION['user_id'];

    $sql = "
        SELECT 
            p.id,
            p.name,
            p.price,
            p.image_url_1,
            uc.quantity,
            uc.color
        FROM user_cart uc
        INNER JOIN products p ON p.id = uc.product_id
        WHERE uc.user_id = ?
          AND uc.quantity > 0
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {

        // 🔒 Quantity safety
        $row['quantity'] = max(1, min((int) $row['quantity'], 99));

        $row['image'] = !empty($row['image_url_1'])
            ? $row['image_url_1']
            : 'images/no-image.png';

        $cart_items[] = $row;

        $total_price += $row['price'] * $row['quantity'];
        $count += $row['quantity'];
    }
}

/* =========================
   GUEST → SESSION CART
   ========================= */ elseif (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {

    foreach ($_SESSION['cart'] as $item) {

        if (
            empty($item['id']) ||
            empty($item['price']) ||
            empty($item['quantity']) ||
            (int) $item['quantity'] <= 0 ||
            (int) $item['quantity'] > 99
        ) {
            continue; // ❌ skip corrupted data
        }

        $item['quantity'] = (int) $item['quantity'];
        $item['image'] = $item['image'] ?? 'images/no-image.png';

        $cart_items[] = $item;

        $total_price += $item['price'] * $item['quantity'];
        $count += $item['quantity'];
    }
}

/* =========================
   RESPONSE
   ========================= */
$response = [
    'html' => '',
    'total' => number_format($total_price, 2),
    'count' => $count
];

/* =========================
   EMPTY CART
   ========================= */
if (empty($cart_items)) {

    $response['html'] = '
        <div style="text-align:center; padding:40px 20px; color:#999;">
            Your cart is empty
        </div>
    ';

} else {

    foreach ($cart_items as $item) {

        $productId = (int) $item['id'];
        $image = htmlspecialchars($item['image']);
        $name = htmlspecialchars($item['name']);
        $color = htmlspecialchars($item['color'] ?? '');
        $price = number_format($item['price'], 2);
        $qty = (int) $item['quantity'];

        $response['html'] .= '
        <div class="sidebar-cart-item">

            <div class="sidebar-cart-img">
                <img src="' . $image . '" alt="' . $name . '">
            </div>

            <div class="sidebar-cart-info">
                <h4>' . $name . '</h4>

                <div class="sidebar-cart-color">
                    Color: ' . $color . '
                </div>

                <div class="sidebar-cart-price">
                    $' . $price . '
                </div>

                <div class="sidebar-cart-controls">
                    <button class="qty-btn minus"
                        onclick="updateCartItem(' . $productId . ', \'decrease\')">−</button>

                    <span class="qty-val">' . $qty . '</span>

                    <button class="qty-btn plus"
                        onclick="updateCartItem(' . $productId . ', \'increase\')">+</button>
                </div>
            </div>

            <button class="sidebar-cart-delete"
                onclick="updateCartItem(' . $productId . ', \'remove\')">
                <i class="fas fa-trash"></i>
            </button>

        </div>';
    }
}

echo json_encode($response);