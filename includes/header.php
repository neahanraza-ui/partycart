
<?php
// Initialize cart count
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
?>

<style>
/* CRITICAL: Ensure sidebar is ALWAYS fixed and off-screen by default */
.cart-overlay {
    position: fixed !important;
    inset: 0 !important;
    background: rgba(0, 0, 0, 0.7) !important;
    backdrop-filter: blur(8px) !important;
    z-index: 9998 !important;
    opacity: 0 !important;
    visibility: hidden !important;
    transition: all 0.5s ease !important;
}
.cart-overlay.open {
    opacity: 1 !important;
    visibility: visible !important;
}
.cart-sidebar {
    position: fixed !important;
    top: 0 !important;
    right: -100% !important;
    width: 450px !important;
    height: 100vh !important;
    background: #0A0A0A !important;
    z-index: 9999 !important;
    display: flex !important;
    flex-direction: column !important;
    transition: right 0.6s cubic-bezier(0.77, 0, 0.175, 1) !important;
    box-shadow: -15px 0 50px rgba(0, 0, 0, 0.8) !important;
}
.cart-sidebar.open {
    right: 0 !important;
}
@media (max-width: 480px) {
    .cart-sidebar { width: 100% !important; }
}
    /* Profile Dropdown Styles */
    .profile-dropdown {
        position: relative !important;
        display: inline-flex !important;
        align-items: center;
    }
    .profile-btn {
        background: transparent !important;
        border: 1px solid rgba(184, 150, 46, 0.3) !important;
        border-radius: 50% !important;
        width: 40px !important;
        height: 40px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        padding: 0 !important;
        transition: all 0.3s ease !important;
        overflow: hidden !important;
    }
    .profile-btn img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .profile-btn:hover {
        border-color: var(--gold-primary) !important;
        transform: translateY(-2px);
    }
    .profile-dropdown-content {
        position: absolute !important;
        top: calc(100% + 15px) !important;
        right: 0 !important;
        width: 220px !important;
        background: rgba(15, 15, 15, 0.98) !important;
        backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(184, 150, 46, 0.2) !important;
        border-radius: 12px !important;
        padding: 8px !important;
        opacity: 0 !important;
        visibility: hidden !important;
        transform: translateY(10px) !important;
        transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1) !important;
        z-index: 10000 !important;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.8) !important;
        display: block !important; /* Ensure it's block when visible */
    }
    .profile-dropdown.active .profile-dropdown-content {
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateY(0) !important;
    }
    .profile-dropdown-content a {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        padding: 12px 15px !important;
        color: rgba(255, 255, 255, 0.8) !important;
        text-decoration: none !important;
        font-size: 14px !important;
        border-radius: 8px !important;
        transition: all 0.2s ease !important;
    }
    .profile-dropdown-content a:hover {
        background: rgba(184, 150, 46, 0.1) !important;
        color: var(--gold-primary) !important;
    }
    /* Search Styles */
    .search-container {
        display: flex;
        align-items: center;
        position: relative;
    }
    .search-form {
        display: flex;
        align-items: center;
        width: 0;
        overflow: hidden;
        transition: width 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        background: transparent;
        margin-right: 5px;
    }
    .search-container.active .search-form {
        width: 180px;
        border-bottom: 1px solid var(--gold-primary);
    }
    .search-input-nav {
        width: 100%;
        background: transparent;
        border: none;
        color: #fff;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        padding: 5px;
        outline: none;
    }
    .search-submit {
        background: transparent;
        border: none;
        color: var(--gold-primary);
        cursor: pointer;
        padding: 0 5px;
        font-size: 12px;
    }
</style>
<script>
    function toggleNavSearch() {
        const container = document.querySelector('.search-container');
        const input = document.querySelector('.search-input-nav');
        container.classList.toggle('active');
        if (container.classList.contains('active')) {
            input.focus();
        }
    }
</script>

<script>
    // Global flag for login status
    const IS_LOGGED_IN = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
</script>

<!-- Navigation -->
<nav class="navbar">
  <!-- Logo -->
  <a href="index.php" class="navbar-logo">LUXORA</a>

  <!-- Navigation Links -->
  <ul class="navbar-links" id="navLinks">
    <li><a href="index.php">Home</a></li>
    <li><a href="products.php">Products</a></li>
    <li><a href="collections.php">Collections</a></li>
    <li><a href="about.php">About</a></li>
  </ul>

  <!-- Icons: Search + Cart + Profile + Mobile Menu -->
  <div class="navbar-icons">
    <!-- Search Icon -->
    <div class="search-container">
        <form action="products.php" method="GET" class="search-form" id="navSearchForm">
            <input type="text" name="search" placeholder="Search..." class="search-input-nav" autocomplete="off">
            <button type="submit" class="search-submit"><i class="fas fa-arrow-right"></i></button>
        </form>
        <i class="fas fa-search nav-icon" id="searchIcon" onclick="toggleNavSearch()"></i>
    </div>

    <!-- Cart Icon -->
    <a href="javascript:void(0)" class="nav-icon cart-button" onclick="openCartSidebar()">
      <i class="fas fa-shopping-bag"></i>
      <span class="cart-count" id="cartCount">
        <?php echo $cart_count; ?>
      </span>
    </a>

    <!-- Profile Dropdown -->
    <?php if(isset($_SESSION['user_id'])): ?>
        <div class="profile-dropdown" id="profileDropdown">
            <button class="profile-btn" id="profileBtn" title="Account Settings" onclick="this.parentElement.classList.toggle('active'); event.stopPropagation();">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['username']); ?>&background=b8962e&color=fff&bold=true" alt="Profile">
            </button>
            <div class="profile-dropdown-content">
                <div style="padding: 12px 15px; border-bottom: 1px solid rgba(255,255,255,0.05); margin-bottom: 5px;">
                    <span style="font-size: 10px; text-transform: uppercase; letter-spacing: 2px; color: rgba(255,255,255,0.4); display: block; margin-bottom: 4px;">Exclusive Member</span>
                    <span style="font-weight: 700; color: var(--gold-primary); font-size: 15px; font-family: var(--font-luxury);"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
                <a href="user_dashboard.php"><i class="fas fa-columns"></i> Dashboard</a>
                <a href="user_dashboard.php#"><i class="fas fa-cog"></i> Settings</a>
                <div style="height: 1px; background: rgba(255,255,255,0.05); margin: 8px 0;"></div>
                <a href="logout.php" style="color: #ff6b6b !important;"><i class="fas fa-sign-out-alt"></i> Log Out</a>
            </div>
        </div>
    <?php else: ?>
        <a href="login.php" class="nav-icon" title="Login">
            <i class="fas fa-user"></i>
        </a>
    <?php endif; ?>

    <!-- Mobile Menu Toggle (positioned last, next to profile) -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
      <span></span>
      <span></span>
      <span></span>
    </button>

</div>
</nav>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay"></div>

<!-- Cart Sidebar Scripts -->
<script src="js/mobile-menu.js"></script>
<script src="js/cart-sidebar.js"></script>
<?php if (true): // Automatically sync cart from server if logged in ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof IS_LOGGED_IN !== 'undefined' && IS_LOGGED_IN && typeof cartManager !== 'undefined') {
            cartManager.loadFromServer();
        }
    });
</script>
<?php if (isset($_SESSION['login_sync_needed'])) unset($_SESSION['login_sync_needed']); ?>
<?php endif; ?>