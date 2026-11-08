<?php
include 'includes/db.php';

// Use session cart as the primary source of truth
$cart_data = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// If cart is empty, redirect back to products
if (empty($cart_data)) {
  header("Location: products.php");
  exit;
}

// Calculate total price
$total_price = 0;
foreach ($cart_data as $item) {
  $total_price += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout - LUXORA</title>
  <!-- Fonts and New Theme -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <link rel="stylesheet" href="styles/pages.css">
  <link rel="stylesheet" href="styles/fonts.css">
  <link rel="stylesheet" href="styles/luxury-theme.css">
  <link rel="stylesheet" href="styles/tailwind.css">
  <link rel="stylesheet" href="styles/theme.css">
  <link rel="stylesheet" href="styles/checkout.css">
  <style>
/* ==============================
   CHECKOUT PAGE (MATCH SIDEBAR)
================================ */

.checkout-page{
  padding: 140px 20px 70px;
  max-width: 1250px;
  margin: 0 auto;
  min-height: 75vh;
}

.checkout-page .section-title{
  font-family: var(--font-luxury);
  color: var(--gold-primary);
  letter-spacing: 2px;
  text-transform: uppercase;
  text-align: center;
  font-size: 42px;
}

.checkout-page .divider{
  width: 90px;
  height: 2px;
  background: linear-gradient(90deg, transparent, var(--gold-primary), transparent);
  margin: 22px auto 45px;
}

/* 2-column layout */
.checkout-container{
  display: grid;
  grid-template-columns: 1.2fr 0.8fr;
  gap: 24px;
  align-items: start;
}

/* Left/Right cards */
.checkout-left,
.checkout-right{
  background: rgba(10, 10, 10, 0.55);
  backdrop-filter: blur(18px);
  border: 1px solid rgba(184, 150, 46, 0.18);
  border-radius: 12px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.65);
}

.checkout-left{
  padding: 30px;
}

.checkout-right{
  padding: 28px;
  position: sticky;
  top: 110px; /* keeps summary visible under fixed navbar */
}

/* Section titles inside form */
.form-section-title{
  font-family: var(--font-luxury);
  color: var(--gold-primary);
  font-size: 22px;
  letter-spacing: 1px;
  margin: 0 0 18px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.form-section-title i{
  opacity: 0.9;
}

/* Form grid */
.form-grid{
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.input-group.full-width{
  grid-column: 1 / -1;
}

/* Labels */
.input-group label{
  display: block;
  margin: 10px 0 8px;
  font-size: 13px;
  letter-spacing: 0.6px;
  text-transform: uppercase;
  color: rgba(243,243,243,0.75);
  font-weight: 500;
}

/* Input wrapper with icon */
.input-wrapper{
  position: relative;
}

.input-wrapper i{
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: rgba(184, 150, 46, 0.75);
  font-size: 14px;
  pointer-events: none;
}

/* Inputs */
.input-field{
  width: 100%;
  padding: 14px 14px 14px 42px; /* space for icon */
  border-radius: 10px;
  border: 1px solid rgba(255,255,255,0.08);
  background: rgba(0,0,0,0.35);
  color: rgba(243,243,243,0.92);
  outline: none;
  transition: all 0.25s ease;
  font-size: 14px;
}

.input-field::placeholder{
  color: rgba(243,243,243,0.35);
}

.input-field:focus{
  border-color: rgba(184, 150, 46, 0.55);
  box-shadow: 0 0 0 3px rgba(184, 150, 46, 0.12);
  background: rgba(0,0,0,0.45);
}

/* Checkout footer / button */
.checkout-footer{
  margin-top: 22px;
}

.checkout-submit-btn{
  width: 100%;
  padding: 18px 18px;
  border-radius: 10px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 2px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  transition: all 0.3s ease;
}

.checkout-submit-btn:hover{
  transform: translateY(-3px);
  box-shadow: 0 10px 30px rgba(184, 150, 46, 0.35);
  filter: brightness(1.05);
}

.checkout-submit-btn:disabled{
  opacity: 0.65;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

/* Right column title */
.checkout-right h3{
  margin: 0 0 18px;
  font-family: var(--font-luxury);
  color: var(--gold-primary);
  letter-spacing: 1px;
  text-transform: uppercase;
}

/* Items list */
.cart-items-list{
  display: flex;
  flex-direction: column;
  gap: 14px;
  max-height: 40vh;
  overflow-y: auto;
  padding-right: 6px;
}

/* Gold scrollbar like sidebar */
.cart-items-list::-webkit-scrollbar{
  width: 4px;
}
.cart-items-list::-webkit-scrollbar-track{
  background: rgba(255,255,255,0.02);
}
.cart-items-list::-webkit-scrollbar-thumb{
  background: var(--gold-primary);
  border-radius: 10px;
}

/* Item card like sidebar item */
.checkout-right .cart-item{
  display: flex;
  gap: 16px;
  padding: 16px;
  border: 1px solid rgba(184, 150, 46, 0.10);
  border-radius: 10px;
  background: rgba(0,0,0,0.25);
  transition: all 0.3s ease;
}

.checkout-right .cart-item:hover{
  background: rgba(184, 150, 46, 0.04);
  transform: translateX(6px);
}

/* Product image */
.product-image{
  width: 86px;
  height: 110px;
  object-fit: cover;
  border-radius: 6px;
  border: 1px solid rgba(255,255,255,0.06);
  box-shadow: 0 8px 22px rgba(0,0,0,0.45);
  transition: transform 0.45s ease;
}

.checkout-right .cart-item:hover .product-image{
  transform: scale(1.06);
}

/* Item info */
.cart-item-info h4{
  margin: 0 0 8px;
  font-family: var(--font-luxury);
  font-size: 16px;
  font-weight: 500;
  color: #fff;
  letter-spacing: 0.6px;
}

.cart-item-info p{
  margin: 5px 0;
  font-size: 13px;
  color: rgba(243,243,243,0.75);
}

/* Coupon section */
.coupon-section{
  margin-top: 20px !important;
  padding-top: 18px !important;
  border-top: 1px solid rgba(184, 150, 46, 0.18) !important;
}

.coupon-section label{
  color: rgba(243,243,243,0.75);
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 0.6px;
}

.coupon-section .input-wrapper{
  margin-top: 10px;
}

.coupon-section input#couponCode{
  padding-left: 14px !important; /* no icon here */
  border-radius: 10px !important;
  background: rgba(0,0,0,0.35) !important;
  border: 1px solid rgba(255,255,255,0.08) !important;
}

.coupon-section input#couponCode:focus{
  border-color: rgba(184, 150, 46, 0.55) !important;
  box-shadow: 0 0 0 3px rgba(184, 150, 46, 0.12) !important;
}

#couponMessage{
  color: rgba(243,243,243,0.7);
}

/* Totals */
.total-price{
  margin-top: 18px;
  padding-top: 18px;
  border-top: 1px solid rgba(184, 150, 46, 0.18);
}

.total-price h3{
  font-family: var(--font-luxury);
  color: var(--gold-primary);
  letter-spacing: 1px;
}

#subtotalRow, #discountRow{
  border-radius: 10px;
  padding: 10px 12px;
  background: rgba(0,0,0,0.20);
  border: 1px solid rgba(255,255,255,0.06);
}

#discountRow{
  border-color: rgba(40,167,69,0.25) !important;
}

#discountRow span{
  color: #4ade80 !important;
}

/* Mobile */
@media (max-width: 900px){
  .checkout-container{
    grid-template-columns: 1fr;
  }
  .checkout-right{
    position: static;
  }
  .form-grid{
    grid-template-columns: 1fr;
  }
  .cart-items-list{
    max-height: 50vh;
  }
}


  </style>
  <link rel="stylesheet" href="styles/theme.css">
  
  <style>
    nav.navbar {
        position: fixed;
        width: 100%;
        z-index: 1000;
    }
  </style>
</head>

<body>
  <?php include 'includes/header.php'; ?>

  <!-- Checkout Page -->
  <section class="checkout-page">
    <h1 class="section-title">Checkout</h1>
    <div class="divider"></div>

    <div class="checkout-container">

      <!-- Left Side: Checkout Form (User & Payment Info) -->
      <div class="checkout-left" id="checkoutContent">
        <h2 class="form-section-title"><i class="fas fa-shipping-fast"></i> Billing Information</h2>
        <form id="checkoutForm" class="billing-form">
          <!-- User Info Fields -->
          <div class="form-grid">
            <div class="input-group">
              <label for="userName">Full Name</label>
              <div class="input-wrapper">
                <i class="fas fa-user"></i>
                <input type="text" id="userName" name="name" placeholder="John Doe" required class="input-field">
              </div>
            </div>
            <div class="input-group">
              <label for="userEmail">Email Address</label>
              <div class="input-wrapper">
                <i class="fas fa-envelope"></i>
                <input type="email" id="userEmail" name="email" placeholder="john@example.com" required class="input-field">
              </div>
            </div>
            <div class="input-group full-width">
              <label for="userAddress">Shipping Address</label>
              <div class="input-wrapper">
                <i class="fas fa-map-marker-alt"></i>
                <input type="text" id="userAddress" name="address" placeholder="123 Luxury Lane, Beverly Hills, CA" required class="input-field">
              </div>
            </div>
            <div class="input-group">
              <label for="userPhone">Phone Number</label>
              <div class="input-wrapper">
                <i class="fas fa-phone"></i>
                <input type="tel" id="userPhone" name="phone" placeholder="+1 (555) 000-0000" required class="input-field">
              </div>
            </div>
          </div>

          <h2 class="form-section-title" style="margin-top: 40px;"><i class="fas fa-credit-card"></i> Payment Details</h2>
          <!-- Payment Info Fields -->
          <div class="form-grid">
            <div class="input-group full-width">
              <label for="card_number">Card Number</label>
              <div class="input-wrapper">
                <i class="fas fa-lock"></i>
                <input type="text" name="card_number" id="card_number" placeholder="0000 0000 0000 0000" required class="input-field">
              </div>
            </div>
            <div class="input-group">
              <label for="expiry_date">Expiry Date</label>
              <div class="input-wrapper">
                <i class="fas fa-calendar-alt"></i>
                <input type="text" name="expiry_date" id="expiry_date" placeholder="MM/YY" required class="input-field">
              </div>
            </div>
            <div class="input-group">
              <label for="cvv">CVV</label>
              <div class="input-wrapper">
                <i class="fas fa-shield-alt"></i>
                <input type="text" name="cvv" id="cvv" placeholder="123" required class="input-field">
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="checkout-footer">
            <button type="button" class="btn-luxury checkout-submit-btn" onclick="placeOrder()">
               <span>Complete Purchase</span>
               <i class="fas fa-arrow-right"></i>
            </button>
          </div>
        </form>
      </div>

      <!-- Right Side: Order Summary -->
      <div class="checkout-right" id="orderSummary">
        <h3>Order Summary</h3>
        <div id="checkoutItemsList" class="cart-items-list">
          <?php foreach ($cart_data as $item): ?>
            <div class="cart-item">
              <img src="<?php echo isset($item['image']) ? $item['image'] : (isset($item['image_url']) ? $item['image_url'] : 'default-image.jpg'); ?>" 
                   alt="<?php echo htmlspecialchars($item['name']); ?>" class="product-image">
              <div class="cart-item-info">
                <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                <?php if (!empty($item['color'])): ?>
                <p>Color: <strong><?php echo htmlspecialchars($item['color']); ?></strong></p>
                <?php endif; ?>
                <p>Qty: <?php echo $item['quantity']; ?></p>
                <p>Price: $<?php echo number_format($item['price'], 2); ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Coupon Section -->
        <div class="coupon-section" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(184, 150, 46, 0.2);">
          <div class="input-group">
            <label for="couponCode">Have a coupon?</label>
            <div class="input-wrapper" style="display: flex; gap: 10px;">
              <input type="text" id="couponCode" placeholder="MAZE-XXXXXX" class="input-field" style="padding-left: 15px !important; margin: 0; height: 45px;">
              <button type="button" class="btn-luxury" onclick="applyCoupon()" style="height: 45px; padding: 0 15px; min-width: 80px; font-size: 14px;">Apply</button>
            </div>
            <p id="couponMessage" style="font-size: 12px; margin-top: 8px; min-height: 18px;"></p>
          </div>
        </div>

        <div class="total-price">
            <div id="subtotalRow" style="display: flex; justify-content: space-between; margin-bottom: 10px; color: var(--text-secondary); font-size: 15px;">
                <span>Subtotal</span>
                <span id="subtotalDisplay">$<?php echo number_format($total_price, 2); ?></span>
            </div>
            <div id="discountRow" style="display: none; justify-content: space-between; margin-bottom: 15px; color: #4ade80; font-size: 15px;">
                <span>Discount (<span id="discountPercent">0</span>%)</span>
                <span id="discountDisplay">-$0.00</span>
            </div>
            <h3 style="display: flex; justify-content: space-between; align-items: center; border: none; margin-top: 10px; padding-top: 10px; border-top: 1px solid var(--gold-primary);">
              <span>Total</span>
              <span id="orderTotalDisplay">$<?php echo number_format($total_price, 2); ?></span>
            </h3>
        </div>
      </div>

    </div>
  </section>

  <?php include 'includes/footer.php'; ?>
  
  <script src="js/cart.js"></script>
  <script src="js/checkout.js"></script>
  <script>
    // Synchronize JS cartManager with PHP session-based cart
    document.addEventListener('DOMContentLoaded', () => {
        const phpCart = <?php echo json_encode($cart_data); ?>;
        if (typeof cartManager !== 'undefined' && cartManager.getCart().length === 0 && phpCart.length > 0) {
            phpCart.forEach(item => {
                const existing = cartManager.cart.find(i => i.id === item.id);
                if (!existing) {
                    cartManager.cart.push(item);
                }
            });
            cartManager.saveCart();
        }
    });
  </script>
</body>
</html>