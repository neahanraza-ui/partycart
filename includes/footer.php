<style>
.newsletter-section {
background-color: #2d2d2d; /* Dark background for the newsletter section */
color: white;
padding: 40px 20px;
margin-bottom: -90px;
text-align: center;
}

.newsletter-content h2 {
font-size: 2rem;
margin-bottom: 10px;
}

.newsletter-content p {
font-size: 1.2rem;
margin-bottom: 20px;
color: rgba(255, 255, 255, 0.8); /* Light color for the paragraph */
}

.newsletter-form {
display: flex;
justify-content: center;
align-items: center;
gap: 10px;
}

.newsletter-input {
padding: 10px;
width: 300px; /* Adjust the width as necessary */
font-size: 1rem;
border: none;
border-radius: 5px;
outline: none;
}

.btn-subscribe {
padding: 10px 20px;
font-size: 1rem;
background-color: #ff9e00;
color: white;
border: none;
border-radius: 5px;
cursor: pointer;
transition: background-color 0.3s ease;
}

.btn-subscribe:hover {
background-color: #e88900;
}
</style>
<!-- Newsletter Section -->
<section class="newsletter-section">
  <div class="newsletter-content">
    <h2>Subscribe to Our Newsletter</h2>
    <p>Get the latest updates and exclusive offers directly to your inbox.</p>
    <form action="subscribe.php" method="POST" class="newsletter-form">
      <input type="email" name="email" placeholder="Enter your email" required class="newsletter-input">
      <button type="submit" class="btn-subscribe">Subscribe</button>
    </form>
  </div>
</section>

<!-- Footer -->
<footer class="footer">
  <div class="footer-content">
    <div class="footer-section">
      <h3>LUXORA</h3>
      <p style="color: rgba(243, 243, 243, 0.7); line-height: 1.8;">
        Redefining luxury through timeless elegance and unparalleled craftsmanship.
      </p>
    </div>
    <div class="footer-section">
      <h3>Quick Links</h3>
      <ul class="footer-links">
        <li><a href="products.php">Products</a></li>
        <li><a href="collections.php">Collections</a></li>
        <li><a href="about.php">About Us</a></li>
        <li><a href="contact.php">Contact</a></li>
      </ul>
    </div>
    <div class="footer-section">
      <h3>Customer Service</h3>
      <ul class="footer-links">
        <li><a href="#">Shipping Info</a></li>
        <li><a href="#">Returns</a></li>
        <li><a href="#">Size Guide</a></li>
        <li><a href="#">FAQ</a></li>
      </ul>
    </div>
    <div class="footer-section">
      <h3>Follow Us</h3>
      <ul class="footer-links">
        <li><a href="#"><i class="fab fa-instagram"></i> Instagram</a></li>
        <li><a href="#"><i class="fab fa-facebook"></i> Facebook</a></li>
        <li><a href="#"><i class="fab fa-pinterest"></i> Pinterest</a></li>
        <li><a href="#"><i class="fab fa-twitter"></i> Twitter</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <p>&copy; 2026 LUXORA. All rights reserved. Crafted with excellence.</p>
  </div>
</footer>

<!-- Cart Sidebar Overlay -->
<div id="cartOverlay" class="cart-overlay" onclick="closeCartSidebar()"></div>

<!-- Cart Sidebar -->
<div id="cartSidebar" class="cart-sidebar">
    <div class="cart-sidebar-header">
        <h3>Shopping Cart</h3>
        <button class="close-cart-btn" onclick="closeCartSidebar()"><i class="fas fa-times"></i></button>
    </div>
    
    <div id="cartSidebarBody" class="cart-sidebar-body">
        <!-- Items loaded via AJAX -->
    </div>
    
    <div class="cart-sidebar-footer">
        <div class="cart-sidebar-total">
            <span>Total:</span>
            <span id="cartSidebarTotal">$0.00</span>
        </div>
        <div class="cart-sidebar-actions">
            <a href="cart.php" class="btn-sidebar-view">Expand Cart</a> <!-- "Expand the Card" per user request -->
            <a href="checkout.php" class="btn-sidebar-checkout">Check to Proceed</a>
        </div>
    </div>
</div>
