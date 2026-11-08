<?php
session_start();
include 'includes/db.php';

$cart = [];
$total_price = 0;

/* LOGGED-IN USER → DB CART */
if (isset($_SESSION['user_id'])) {
  $uid = $_SESSION['user_id'];

  $sql = "
        SELECT p.id, p.name, p.price, p.image_url_1, uc.quantity, uc.color
        FROM user_cart uc
        JOIN products p ON p.id = uc.product_id
        WHERE uc.user_id = ?
    ";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $uid);
  $stmt->execute();
  $result = $stmt->get_result();

  while ($row = $result->fetch_assoc()) {
    $row['image'] = $row['image_url_1'] ?? 'images/no-image.png';
    $cart[] = $row;
  }
}
/* GUEST → SESSION CART */ else if (isset($_SESSION['cart'])) {
  $cart = $_SESSION['cart'];
}
?>
 
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shopping Cart - LUXORA</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <link rel="stylesheet" href="styles/pages.css">
  <link rel="stylesheet" href="styles/fonts.css">
  <link rel="stylesheet" href="styles/luxury-theme.css">
  <link rel="stylesheet" href="styles/tailwind.css">
  <link rel="stylesheet" href="styles/theme.css">
  <style>
/* ==============================
   CART PAGE (MATCH SIDEBAR STYLE)
================================ */

.cart-page{
  max-width: 1200px;
  margin: 0 auto;
  padding: 140px 20px 60px; /* top spacing for fixed header */
  min-height: 70vh;
}

.cart-page .section-title{
  font-family: var(--font-luxury);
  color: var(--gold-primary);
  letter-spacing: 2px;
  text-transform: uppercase;
  text-align: center;
}

.cart-page .divider{
  width: 90px;
  height: 2px;
  background: linear-gradient(90deg, transparent, var(--gold-primary), transparent);
  margin: 22px auto 45px;
}

/* Container */
.cart-items{
  background: rgba(10, 10, 10, 0.55);
  backdrop-filter: blur(18px);
  border: 1px solid rgba(184, 150, 46, 0.18);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0,0,0,0.65);
}

/* Item Row */
.cart-item{
  display: flex;
  gap: 22px;
  padding: 26px;
  border-bottom: 1px solid rgba(184, 150, 46, 0.10);
  position: relative;
  transition: all 0.35s ease;
}

.cart-item:last-child{
  border-bottom: none;
}

.cart-item:hover{
  background: rgba(184, 150, 46, 0.04);
  transform: translateX(6px);
}

/* Product Image */
.cart-item img{
  width: 110px;
  height: 140px;
  object-fit: cover;
  border-radius: 6px;
  border: 1px solid rgba(255,255,255,0.06);
  box-shadow: 0 8px 22px rgba(0,0,0,0.45);
  transition: transform 0.45s ease;
}

.cart-item:hover img{
  transform: scale(1.06);
}

/* Info */
.cart-item-info{
  flex: 1;
  padding-top: 2px;
}

.cart-item-info h3{
  font-family: var(--font-luxury);
  font-size: 18px;
  color: #fff;
  font-weight: 500;
  margin: 0 0 10px;
  letter-spacing: 0.6px;
}

.cart-item-info p{
  margin: 6px 0;
  font-size: 14px;
  color: rgba(243,243,243,0.75);
}

.cart-item-info p strong{
  color: rgba(243,243,243,0.9);
}

/* Actions */
.cart-item-actions{
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  justify-content: center;
  gap: 12px;
  min-width: 90px;
}

.cart-form{
  margin: 0;
}

/* Increase button like premium qty controls */
.btn-increase{
  width: 44px;
  height: 44px;
  border-radius: 10px;
  border: 1px solid rgba(184, 150, 46, 0.35);
  background: rgba(0,0,0,0.35);
  color: var(--gold-primary);
  font-size: 20px;
  cursor: pointer;
  transition: all 0.25s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-increase:hover{
  background: rgba(184, 150, 46, 0.10);
  border-color: var(--gold-primary);
  transform: translateY(-2px);
  box-shadow: 0 10px 22px rgba(184, 150, 46, 0.10);
}

/* Delete button */
.btn-delete{
  width: 44px;
  height: 44px;
  border-radius: 10px;
  border: 1px solid rgba(255,255,255,0.08);
  background: rgba(0,0,0,0.25);
  color: rgba(255,255,255,0.35);
  cursor: pointer;
  transition: all 0.25s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-delete:hover{
  border-color: rgba(220,53,69,0.45);
  color: #d4183d;
  transform: translateY(-2px);
  box-shadow: 0 10px 22px rgba(220,53,69,0.10);
}

/* Summary Card */
.cart-summary{
  margin-top: 22px;
  padding: 26px;
  border-radius: 12px;
  background: rgba(15, 15, 15, 0.75);
  backdrop-filter: blur(14px);
  border: 1px solid rgba(184, 150, 46, 0.18);
  box-shadow: 0 18px 50px rgba(0,0,0,0.55);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.cart-summary h3{
  margin: 0;
  font-family: var(--font-luxury);
  color: var(--gold-primary);
  font-size: 22px;
  letter-spacing: 1px;
}

/* Make checkout button match sidebar checkout vibe */
.cart-summary .btn-luxury{
  padding: 16px 22px;
  border-radius: 10px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 2px;
}

/* ==============================
   LUXURY EMPTY CART STATE
================================ */

.empty-cart-message{
  background: linear-gradient(145deg, rgba(15,15,15,0.75), rgba(10,10,10,0.65));
  border: 1px solid rgba(184, 150, 46, 0.25);
  border-radius: 14px;
  box-shadow: 0 25px 70px rgba(0,0,0,0.7);
  padding: 70px 40px;
  text-align: center;
  position: relative;
  overflow: hidden;
}

/* subtle gold glow */
.empty-cart-message::before{
  content: "";
  position: absolute;
  top: -40%;
  left: -40%;
  width: 180%;
  height: 180%;
  background: radial-gradient(circle at center, rgba(184,150,46,0.08), transparent 60%);
  animation: goldPulse 6s infinite linear;
}

@keyframes goldPulse{
  0%{ transform: rotate(0deg); }
  100%{ transform: rotate(360deg); }
}

.empty-cart-message i{
  font-size: 42px;
  color: var(--gold-primary);
  margin-bottom: 18px;
  display: block;
  opacity: 0.9;
}

.empty-cart-message h2{
  font-family: var(--font-luxury);
  color: #fff;
  font-size: 22px;
  letter-spacing: 2px;
  margin-bottom: 12px;
}

.empty-cart-message p{
  color: rgba(255,255,255,0.6);
  margin-bottom: 30px;
  font-size: 15px;
}

.empty-cart-message .btn-luxury{
  padding: 14px 28px;
  font-size: 13px;
  letter-spacing: 2px;
  border-radius: 10px;
  font-weight: 700;
}

/* Gold Scrollbar (if cart list becomes scrollable) */
.cart-items{
  max-height: 70vh;
  overflow-y: auto;
  scroll-behavior: smooth;
}

.cart-items::-webkit-scrollbar{
  width: 4px;
}

.cart-items::-webkit-scrollbar-track{
  background: rgba(255,255,255,0.02);
}

.cart-items::-webkit-scrollbar-thumb{
  background: var(--gold-primary);
  border-radius: 10px;
}

/* Responsive */
@media (max-width: 768px){
  .cart-item{
    gap: 16px;
    padding: 18px;
  }

  .cart-item img{
    width: 90px;
    height: 120px;
  }

  .cart-item-actions{
    min-width: 70px;
  }

  .cart-summary{
    flex-direction: column;
    align-items: stretch;
  }

  .cart-summary form,
  .cart-summary .btn-luxury{
    width: 100%;
  }
}

  </style>
</head>

<body>
  <?php include 'includes/header.php'; ?>

    <!-- Cart Page -->
  <section class="cart-page">
    <h1 class="section-title">Shopping Cart</h1>
    <div class="divider"></div>

<div class="cart-items">
<?php if (!empty($cart)): ?>
    <?php foreach ($cart as $key => $item): ?>
      <?php
      $subtotal = $item['price'] * $item['quantity'];
      $total_price += $subtotal;
      ?>

      <div class="cart-item">
        <img src="<?= htmlspecialchars($item['image'] ?? 'default-image.jpg') ?>">

        <div class="cart-item-info">
          <h3><?= htmlspecialchars($item['name']) ?></h3>
          <?php if (!empty($item['color'])): ?>
          <p>Color: <strong><?= htmlspecialchars($item['color']) ?></strong></p>
          <?php endif; ?>
          <p>Price: $<?= number_format($item['price'], 2) ?></p>
          <p>Quantity: <?= (int) $item['quantity'] ?></p>
          <p>Subtotal: $<?= number_format($subtotal, 2) ?></p>
        </div>

        <!-- ACTIONS (INSIDE LOOP) -->
        <div class="cart-item-actions">
          <div class="qty-controls">
            <button onclick="updateCartItem(<?= (int) $item['id'] ?>,'decrease')">−</button>
            <span><?= (int) $item['quantity'] ?></span>
            <button onclick="updateCartItem(<?= (int) $item['id'] ?>,'increase')">+</button>
          </div>

          <button onclick="updateCartItem(<?= (int) $item['id'] ?>,'remove')" class="btn-delete">
            <i class="fa fa-trash"></i>
          </button>
        </div>
      </div>

    <?php endforeach; ?>
  <?php else: ?>
<div class="empty-cart-message">
  <i class="fa-solid fa-bag-shopping"></i>
  <h2>Your Cart is Empty</h2>
  <p>Explore our exclusive collection and elevate your style.</p>
  <a href="products.php" class="btn-luxury">Discover Products</a>
</div>
  <?php endif; ?>
</div>

    <?php if ($total_price > 0): ?>
    <div class="cart-summary">
      <h3>Total: $<?php echo number_format($total_price, 2); ?></h3>
  
      <!-- Checkout Button -->
      <form action="checkout.php" method="POST">
        <input type="hidden" name="cart_data" value='<?php echo base64_encode(serialize($_SESSION['cart'])); ?>'>
        <button type="submit" class="btn-luxury">Proceed to Checkout</button>
      </form>
    </div>
    <?php endif; ?>
  </section>


  <!-- Footer -->
  <?php include 'includes/footer.php'; ?>
<script>
  async function updateCartItem(productId, action) {
    const res = await fetch('update_cart_ajax.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: productId, action })
    });

    const data = await res.json();

    if (data.success) {
      location.reload();
    } else {
      alert(data.message || 'Cart update failed');
    }
  }
</script>


</html>