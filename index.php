<?php
include 'includes/db.php';

// Fetch best sellers from database
$sql_best_sellers = "SELECT * FROM products ORDER BY sales DESC LIMIT 4";
$result_best_sellers = $conn->query($sql_best_sellers);

if ($result_best_sellers->num_rows > 0) {
  $best_sellers = $result_best_sellers->fetch_all(MYSQLI_ASSOC);  // Fetch all results as associative array
} else {
  $best_sellers = [];  // Return an empty array if no results
}

// Fetch featured collections from database
$sql_featured_collections = "SELECT * FROM products WHERE featured = 1 LIMIT 3";
$result_featured_collections = $conn->query($sql_featured_collections);

if ($result_featured_collections->num_rows > 0) {
  $featured_collections = $result_featured_collections->fetch_all(MYSQLI_ASSOC);
} else {
  $featured_collections = [];  // Return an empty array if no results
}

// Dynamic Meta Tags
include 'includes/meta_helper.php';
$meta_tags = fetch_page_meta($conn, 'index');
$page_title = $meta_tags['meta_title'];
$meta_description = $meta_tags['meta_description'];
$meta_keywords = $meta_tags['meta_keywords'];

// Example of dynamic image for Open Graph (OG) meta tags
$og_image = isset($best_sellers[0]['image_url_1']) ? $best_sellers[0]['image_url_1'] : 'path/to/default-image.jpg';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($page_title); ?></title>

  <!-- Dynamic Meta Tags -->
  <meta property="og:description" content="<?php echo $page_description; ?>" />
  <meta property="og:image" content="<?php echo $og_image; ?>" />

  <!-- External Resources -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="styles/pages.css">
  <link rel="stylesheet" href="styles/fonts.css">
  <link rel="stylesheet" href="styles/luxury-theme.css">
  <link rel="stylesheet" href="styles/tailwind.css">
  <link rel="stylesheet" href="styles/theme.css">
  <style>.game-discount-section {
  position: relative; /* Ensures that the content is positioned relative to this section */
  padding: 80px 20px; /* Add some padding for space */
  text-align: center;
  background-color: #f9f9f9; /* Light background to differentiate */
  margin-top: 30px;
  color: white; /* Set text color to white for contrast against the background */
  background-image: url('uploads/istockphoto-1278580953-612x612.jpg'); /* Set your background image here */
  background-size: cover; /* Makes the background image cover the entire section */
  background-position: center center; /* Center the background image */
  background-repeat: no-repeat; /* Ensure the image doesn't repeat */
}

.game-discount-content {
  position: relative; /* Keep the text on top of the image */
  z-index: 2; /* Ensure that text stays on top of the background */
}

.game-discount-content h3 {
  font-size: 2.5rem;
  margin-bottom: 20px;
  color: #fff; /* White text for visibility */
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); /* Optional shadow for text visibility */
}

.game-discount-content p {
  font-size: 1.2rem;
  color: #fff; /* White text for visibility */
  margin-bottom: 20px;
  text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5); /* Optional shadow for text visibility */
}

.btn-play-game {
  background-color: #ff9e00; /* Button color */
  color: white;
  padding: 15px 30px;
  font-size: 1.2rem;
  border: none;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.btn-play-game:hover {
  background-color: #e88900; /* Darker on hover */
}
/* ================= ROOT VARIABLES ================= */
:root{
  --gold-primary:#b8962e;
  --gold-secondary:#e6c86e;
  --font-luxury:'Poppins', sans-serif;
}

/* ================= PRODUCT CARD ================= */
.product-card{
  background:rgba(10,10,10,0.85);
  backdrop-filter:blur(18px);
  border:1px solid rgba(184,150,46,0.12);
  border-radius:10px;
  box-shadow:0 16px 40px rgba(0,0,0,0.55);
  overflow:hidden;
  display:flex;
  flex-direction:column;
  transition:
    background .35s ease,
    border .35s ease,
    box-shadow .45s ease,
    transform .45s cubic-bezier(.22,1,.36,1);
  will-change:transform;
}

/* Hover lift */
.product-card:hover{
  background:rgba(184,150,46,0.03);
  transform:translateY(-6px);
  box-shadow:0 22px 55px rgba(0,0,0,.6);
  border-color:rgba(184,150,46,.25);
}

/* ================= IMAGE ================= */
.product-image-wrapper img{
  width:100%;
  height:165px;
  object-fit:cover;
  transition:transform .6s cubic-bezier(.22,1,.36,1);
}

.product-card:hover img{
  transform:scale(1.06);
}

/* ================= TEXT ================= */
.product-name{
  margin:10px 12px 4px;
  font-family:var(--font-luxury);
  font-size:15px;
  font-weight:500;
  color:#fff;
  transition:color .3s ease;
}

.product-description{
  margin:0 12px 6px;
  font-size:12.5px;
  line-height:1.35;
  color:rgba(243,243,243,.7);
  transition:color .3s ease;
}

/* ================= PRICE ================= */
.product-price{
  margin:6px 12px auto;
  color:var(--gold-primary);
  font-weight:700;
  font-size:14px;
}

/* ================= BUTTON WRAPPER ================= */
.product-actions{
  display:flex;
  gap:8px;
  padding:10px 12px 12px;
}

/* ================= VIEW BUTTON ================= */
.btn-view-details{
  flex:1;
  padding:12px;
  font-size:11px;
  font-weight:700;
  letter-spacing:1.5px;
  text-transform:uppercase;
  color:var(--gold-primary);
  border:1px solid rgba(184,150,46,0.4);
  background:transparent;
  border-radius:2px;
  display:flex;
  justify-content:center;
  align-items:center;
  gap:6px;
  text-decoration:none;
  transition:all .35s cubic-bezier(.22,1,.36,1);
}

.btn-view-details::after{
  content:'\f105';
  font-family:'Font Awesome 6 Free';
  font-weight:900;
}

.btn-view-details:hover{
  background:rgba(184,150,46,.08);
  transform:translateY(-3px) scale(1.02);
}

/* ================= ADD TO CART ================= */
.btn-add-to-cart{
  flex:1;
  padding:13px;
  font-size:11px;
  font-weight:900;
  letter-spacing:1.5px;
  text-transform:uppercase;
  border:none;
  border-radius:2px;
  background:linear-gradient(135deg,var(--gold-primary),var(--gold-secondary));
  color:#000;
  cursor:pointer;
  transition:all .35s cubic-bezier(.22,1,.36,1);
}

.btn-add-to-cart:hover{
  transform:translateY(-3px) scale(1.02);
  box-shadow:0 10px 25px rgba(184,150,46,.35);
}

.btn-add-to-cart:active,
.btn-view-details:active{
  transform:scale(.97);
}

/* ================= RESPONSIVE ================= */

/* ≤1200px */
@media (max-width:1200px){
  .grid-4{
    grid-template-columns:repeat(3,1fr);
  }
}

/* ≤992px */
@media (max-width:992px){
  .grid-4{
    grid-template-columns:repeat(2,1fr);
  }

  .product-image-wrapper img{
    height:185px;
  }
}

/* ≤768px (Mobile) */
@media (max-width:768px){
  .grid-4{
    grid-template-columns:1fr;
  }

  .product-card{
    transform:none;
  }

  .product-card:hover{
    transform:none;
  }

  .product-image-wrapper img{
    height:210px;
  }

  .product-name{
    font-size:16px;
  }

  .product-description{
    font-size:13px;
  }
}

/* ≤480px */
@media (max-width:480px){
  .product-image-wrapper img{
    height:190px;
  }

  .product-actions{
    flex-direction:column;
  }

  .btn-add-to-cart,
  .btn-view-details{
    width:100%;
  }
}

/* ================= MOBILE HOVER FIX ================= */
@media (hover:none){
  .product-card:hover,
  .btn-add-to-cart:hover,
  .btn-view-details:hover{
    transform:none;
    box-shadow:none;
  }
}



</style>
</head>

<body>
  <?php include 'includes/header.php'; ?>



  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-content fade-in">
      <h1 class="hero-title">Timeless Elegance</h1>
      <p class="hero-subtitle">Experience luxury redefined with our exclusive collection</p>
      <button class="btn-luxury gold-glow" onclick="window.location.href='products.php'">Explore Collection</button>
    </div>
    <div class="hero-overlay"></div>
  </section>

<section class="section">
  <h2 class="section-title">Best Sellers</h2>

  <div class="grid-4">
    <?php foreach ($best_sellers as $product): ?>

      <?php
      $image = '';
      for ($i = 1; $i <= 5; $i++) {
        if (!empty($product["image_url_$i"])) {
          $image = $product["image_url_$i"];
          break;
        }
      }
      ?>

      <div class="product-card">

        <!-- Image -->
        <div class="product-image-wrapper">
          <img src="<?php echo htmlspecialchars($image ?: 'assets/default.jpg'); ?>"
               alt="<?php echo htmlspecialchars($product['name']); ?>"
               class="product-image">
        </div>

        <!-- Info -->
        <div class="product-info">
          <h3 class="product-name">
            <?php echo htmlspecialchars($product['name']); ?>
          </h3>

          <!-- INCOMPLETE DESCRIPTION -->
          <p class="product-description">
            <?php echo htmlspecialchars(substr($product['description'], 0, 140)); ?>...
          </p>

          <div class="product-price">
            $<?php echo number_format($product['price'], 2); ?>
          </div>

          <!-- Buttons -->
          <div class="product-actions">
            <a href="product-details.php?id=<?php echo $product['id']; ?>"
               class="btn-view-details">
              View
            </a>
<form method="POST" class="add-to-cart-form">
  <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
  <input type="hidden" name="product_name" value="<?php echo $product['name']; ?>">
  <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
  <input type="hidden" name="product_image" value="<?php echo $product['image_url_1']; ?>">
  <button type="submit" class="btn-add-to-cart">Add to Cart</button>
</form>

          </div>
        </div>

      </div>

    <?php endforeach; ?>
  </div>
</section>
<!-- New Section After Best Sellers for Game and Discount -->
<section class="game-discount-section">
  <div class="game-discount-content">
    <h3>Get a Discount by Playing a Game</h3>
    <p>Play our fun game and earn a discount on your next purchase! It's easy and exciting!</p>
    <button class="btn-play-game" onclick="window.location.href='game/Maze-Game-main/index.php'">Play Game</button>
  </div>
</section>

<section class="featured-collections featured">
  <h2 class="section-title" style="margin-top:-30px; margin-bottom:70px;">Featured Collections</h2>
  <div class="grid-3">

    <?php foreach ($featured_collections as $product): ?>

      <?php
      $image = '';
      for ($i = 1; $i <= 5; $i++) {
        if (!empty($product["image_url_$i"])) {
          $image = $product["image_url_$i"];
          break;
        }
      }
      ?>

      <div class="product-card">

        <!-- Image -->
        <div class="product-image-wrapper">
          <img src="<?php echo htmlspecialchars($image ?: 'assets/default.jpg'); ?>"
            alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
        </div>

        <!-- Info -->
        <div class="product-info">
          <h3 class="product-name">
            <?php echo htmlspecialchars($product['name']); ?>
          </h3>

          <p class="product-description">
            <?php echo htmlspecialchars(substr($product['description'], 0, 120)); ?>...
          </p>

          <div class="product-price">
            $
            <?php echo number_format($product['price'], 2); ?>
          </div>

          <div class="product-actions">
<form method="POST" class="add-to-cart-form">
  <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
  <input type="hidden" name="product_name" value="<?php echo $product['name']; ?>">
  <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
  <input type="hidden" name="product_image" value="<?php echo $product['image_url_1']; ?>">
  <button type="submit" class="btn-add-to-cart">Add to Cart</button>
</form>

            <a href="product-details.php?id=<?php echo $product['id']; ?>" class="btn-view-details">
              View
            </a>
          </div>
        </div>

      </div>

    <?php endforeach; ?>

  </div>
</section>

<?php include 'includes/footer.php'; ?>


  <script src="js/products-data.js"></script>
  <script src="js/cart.js"></script>
  <script src="js/animations.js"></script>
</body>

</html>