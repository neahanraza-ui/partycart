<?php
session_start();
include 'includes/db.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Fetch product details
$sql_product = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql_product);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

// Check if product exists
if (!$product) {
    echo "Product not found.";
    exit;
}

// Generate slug from product name
function generate_slug($string) {
    $string = strtolower($string);
    $string = preg_replace('/\s+/', '-', $string);
    $string = preg_replace('/[^a-z0-9-]/', '', $string);
    return $string;
}
$product_slug = !empty($product['slug']) ? $product['slug'] : generate_slug($product['name']);

// ===== Dynamic SEO Meta Data =====
$page_title = !empty($product['meta_title']) ? $product['meta_title'] : $product['name'] . ' | LUXORA';
$page_description = !empty($product['meta_description']) ? $product['meta_description'] : substr(strip_tags($product['description']), 0, 160);
$page_keywords = !empty($product['meta_keywords']) ? $product['meta_keywords'] : 'luxury, ecommerce, exclusive collection';
$og_image = !empty($product['image_url_1']) ? 'https://yourdomain.com/' . $product['image_url_1'] : 'https://yourdomain.com/admin/uploads/no-image.png';
$product_url = 'https://yourdomain.com/product/' . $product_slug;

if (isset($_POST['add_to_cart'])) {
  $cart_product = [
    'id' => $_POST['product_id'],
    'name' => $_POST['product_name'],
    'price' => $_POST['product_price'],
    'image' => $_POST['product_image'],
    'color' => isset($_POST['product_color']) ? $_POST['product_color'] : '',
    'quantity' => 1,
  ];

  if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
  }

  $found = false;
  foreach ($_SESSION['cart'] as &$cart_item) {
    if ($cart_item['id'] == $cart_product['id'] && $cart_item['color'] == $cart_product['color']) {
      $cart_item['quantity'] += 1;
      $found = true;
      break;
    }
  }

  if (!$found) {
    $_SESSION['cart'][] = $cart_product;
  }

  header('Location: cart.php');
  exit();
}


// Fetch Related Products
$sql_related = "SELECT * FROM products WHERE category_id = ? AND id != ? LIMIT 4";
$stmt_rel = $conn->prepare($sql_related);
$stmt_rel->bind_param("ii", $product['category_id'], $product_id);
$stmt_rel->execute();
$related_products = $stmt_rel->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($page_title); ?></title>

<meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
<meta name="keywords" content="<?php echo htmlspecialchars($page_keywords); ?>">

<meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>" />
<meta property="og:description" content="<?php echo htmlspecialchars($page_description); ?>" />
<meta property="og:image" content="<?php echo htmlspecialchars($og_image); ?>" />
<meta property="og:url" content="<?php echo htmlspecialchars($product_url); ?>" />
<meta property=" og:type" content="product" />


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
  <style>
/* --------------------------
   Black & Gold Theme
--------------------------- */
/* ==============================
   PRODUCT DETAILS (SIDEBAR STYLE)
================================ */

body{
  background: #0a0a0a;
  color: rgba(243,243,243,0.9);
  font-family: 'Inter', sans-serif;
}

/* Page wrapper */
.product-details{
  padding: 14px 20px 70px;
}

.product-details-container{
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1.05fr 0.95fr;
  gap: 28px;
  align-items: start;
}

/* Glass cards like sidebar */
.product-images-gallery,
.product-info{
  background: rgba(10, 10, 10, 0.55);
  backdrop-filter: blur(18px);
  border: 1px solid rgba(184, 150, 46, 0.18);
  border-radius: 12px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.65);
  overflow: hidden;
}

/* Images card */
.product-images-gallery{
  padding: 22px;
}

.product-images-gallery .main-image{
  border-radius: 12px;
  overflow: hidden;
  border: 1px solid rgba(255,255,255,0.06);
  box-shadow: 0 10px 30px rgba(0,0,0,0.55);
}

.product-images-gallery .main-image img{
  width: 100%;
  height: 430px;
  object-fit: cover;
  display: block;
  transition: transform 0.6s ease;
}

.product-images-gallery:hover .main-image img{
  transform: scale(1.03);
}

/* Thumbnails */
.product-images-gallery .thumbnails{
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  margin-top: 14px;
}

.product-images-gallery .thumbnails img.thumb{
  width: 78px;
  height: 78px;
  object-fit: cover;
  border-radius: 10px;
  cursor: pointer;
  border: 1px solid rgba(255,255,255,0.08);
  background: rgba(0,0,0,0.35);
  transition: all 0.25s ease;
  box-shadow: 0 8px 18px rgba(0,0,0,0.45);
  opacity: 0.85;
}

.product-images-gallery .thumbnails img.thumb:hover{
  border-color: rgba(184, 150, 46, 0.55);
  transform: translateY(-2px);
  opacity: 1;
}

/* Info card */
.product-info{
  padding: 28px;
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.product-info h1{
  font-family: var(--font-luxury);
  font-size: 34px;
  color: #fff;
  letter-spacing: 0.8px;
  margin: 0;
}

.product-description{
  color: rgba(243,243,243,0.72);
  line-height: 1.7;
  margin-bottom: 0;
  font-size: 14.5px;
}

/* Price block */
.product-price{
  display: flex;
  align-items: baseline;
  gap: 12px;
  padding: 14px 16px;
  border-radius: 12px;
  background: rgba(0,0,0,0.25);
  border: 1px solid rgba(255,255,255,0.06);
}

.product-price .price{
  font-family: var(--font-luxury);
  color: var(--gold-primary);
  font-size: 26px;
  letter-spacing: 1px;
}

.product-price .old-price{
  color: rgba(243,243,243,0.35);
  text-decoration: line-through;
  font-size: 14px;
}

/* Add to cart form */
.add-to-cart-form{
  margin-top: 6px;
}

.btn-add-to-cart{
  width: 100%;
  padding: 18px 18px;
  border: none;
  cursor: pointer;
  border-radius: 12px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 2px;
  background: linear-gradient(135deg, var(--gold-primary), var(--gold-secondary));
  color: #000;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
}

.btn-add-to-cart:hover{
  transform: translateY(-3px);
  box-shadow: 0 12px 35px rgba(184, 150, 46, 0.35);
  filter: brightness(1.06);
}

.btn-add-to-cart:active{
  transform: translateY(-1px);
}

/* Related products */
.related-products-section{
  grid-column: 1 / -1;
  margin-top: 20px;
}

.related-products-section h2{
  font-family: var(--font-luxury);
  color: var(--gold-primary);
  letter-spacing: 2px;
  text-transform: uppercase;
  font-size: 22px;
  margin: 26px 0 16px;
}

.related-products-grid{
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
  gap: 16px;
}

/* Related card: same vibe */
.related-product{
  background: rgba(10, 10, 10, 0.55);
  backdrop-filter: blur(18px);
  border: 1px solid rgba(184, 150, 46, 0.14);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 18px 45px rgba(0,0,0,0.55);
  transition: all 0.35s ease;
}

.related-product:hover{
  transform: translateY(-6px);
  border-color: rgba(184, 150, 46, 0.28);
}

.related-product img{
  width: 100%;
  height: 190px;
  object-fit: cover;
  display: block;
  transition: transform 0.6s ease;
}

.related-product:hover img{
  transform: scale(1.05);
}

.related-product h3{
  margin: 14px 14px 6px;
  font-family: var(--font-luxury);
  font-size: 16px;
  color: #fff;
  letter-spacing: 0.5px;
}

.related-product p{
  margin: 0 14px 12px;
  color: var(--gold-primary);
  font-weight: 700;
}

.related-product a{
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  margin: 0 14px 16px;
  padding: 12px 14px;
  border-radius: 10px;
  text-decoration: none;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  font-size: 12px;
  font-weight: 700;
  color: var(--gold-primary);
  border: 1px solid rgba(184, 150, 46, 0.35);
  background: rgba(0,0,0,0.25);
  transition: all 0.35s ease;
}

.related-product a:hover{
  background: rgba(184, 150, 46, 0.08);
  border-color: var(--gold-primary);
  transform: translateY(-2px);
  box-shadow: 0 8px 18px rgba(184, 150, 46, 0.10);
}

.related-product a::after{
  content: '\f105';
  font-family: 'Font Awesome 6 Free';
  font-weight: 900;
  transition: transform 0.3s ease;
}

.related-product a:hover::after{
  transform: translateX(5px);
}

/* Responsive */
@media (max-width: 950px){
  .product-details-container{
    grid-template-columns: 1fr;
  }
  .product-images-gallery .main-image img{
    height: 360px;
  }
}
.color-options {
  display: flex;
  gap: 20px;
  margin: 10px 0;
}
.thumb.active {
    border: 2px solid #fff7f7;
}

.color-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  cursor: pointer;
  font-size: 14px;
  position: relative;
}

/* Hide radio but keep it selectable */
.color-item input[type="radio"] {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

.color-circle {
  width: 35px;
  height: 35px;
  border-radius: 50%;
  border: 2px solid #ccc;
  margin-bottom: 5px;
  transition: 0.3s ease;
}

/* When selected */
.color-item input[type="radio"]:checked + .color-circle {
  border: 3px solid black;
  transform: scale(1.1);
}


.zoom-container {
    position: relative;
    overflow: hidden;
    cursor: zoom-in;
}

.zoom-container img {
    width: 100%;
    transition: transform 0.1s ease;
}




</style>

</head>
<body>
  <?php include 'includes/header.php'; ?>


  <!-- Product Details -->
  <section class="product-details">
    <div class="product-details-container">
      <!-- Product Images -->
<!-- Product Images -->
<div class="product-images-gallery">
<div class="main-image">
  <div class="zoom-container">
    <img id="mainProductImage" 
         src="<?php echo isset($product['image_url_1']) ? $product['image_url_1'] : 'images/no-image.png'; ?>"
      alt="<?php echo htmlspecialchars($product['name']); ?>">
  </div>
  </div>

  <div class="thumbnails">
    <?php for ($i = 1; $i <= 5; $i++):
      $img = $product["image_url_$i"] ?? null;
      if ($img): ?>
        <img class="thumb" src="<?php echo $img; ?>" alt="Thumbnail <?php echo $i; ?>">
      <?php endif; endfor; ?>
  </div>
</div>
<!-- Product Info -->
<div class="product-info">
  <h1><?php echo htmlspecialchars($product['name']); ?></h1>
  <p class="product-description"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
  <p><strong>Stock:</strong> <?php echo (int)$product['stock_quantity']; ?></p>

  <div class="product-price">
    <span class="price">$<?php echo number_format($product['price'], 2); ?></span>
    <?php if (!empty($product['old_price'])): ?>
      <span class="old-price">$<?php echo number_format($product['old_price'], 2); ?></span>
    <?php endif; ?>
  </div>

<form method="POST" class="add-to-cart-form">
    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
  <input type="hidden" name="product_name" value="<?php echo $product['name']; ?>">
  <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
  <input type="hidden" name="product_image" value="<?php echo $product['image_url_1']; ?>">
  <input type="hidden" name="add_to_cart" value="1">

  <?php
  $colors = [];
  if (!empty($product['color'])) {
    $colors = explode(',', $product['color']);
  }
  ?>
  <?php if (!empty($colors)): ?>
    <p><strong>Select Color:</strong></p>
  <div class="color-options" style="margin-bottom: 20px;">
    <?php foreach ($colors as $color):
      $color = trim($color); ?>
        
          <label class="color-item">
            <input type="radio" 
                   name="product_color" 
                   value="<?php echo htmlspecialchars($color); ?>">

      <span class="color-circle" style="background-color: <?php echo strtolower($color); ?>;">
      </span>

      <span class="color-name">
        <?php echo htmlspecialchars($color); ?>
      </span>
        </label>

    <?php endforeach; ?>
  </div>
<?php endif; ?>

  <button type="submit" class="btn-add-to-cart">Add to Cart</button>
</form>



</div>

</div>



      <!-- Product Info Section -->
     
      <!-- Related Products -->
      <div class="related-products-section">
        <h2>You May Also Like</h2>
        <div class="related-products-grid">
          <?php while ($related_product = $related_products->fetch_assoc()): ?>
            <div class="related-product">
            <img src="<?php echo isset($related_product['image_url_1']) ? $related_product['image_url_1'] : 'path/to/default-image.jpg'; ?>" alt="Related Product">
          <h3>
            <?php echo isset($related_product['name']) ? $related_product['name'] : 'Related Product'; ?>
          </h3>
          <p>$<?php echo isset($related_product['price']) ? number_format($related_product['price'], 2) : '0.00'; ?></p>
          <a href="product-details.php?id=<?php echo isset($related_product['id']) ? $related_product['id'] : '#'; ?>">View Details</a>
        </div>
        <?php endwhile; ?>
      </div>
    </div>
  </section>

 <?php include 'includes/footer.php'; ?>
<script>
  document.querySelectorAll('input[name="product_color"]').forEach(radio => {
    radio.addEventListener('click', function () {
      if (this.wasChecked) {
        this.checked = false;
      }
      document.querySelectorAll('input[name="product_color"]').forEach(r => r.wasChecked = false);
      this.wasChecked = this.checked;
    });
  });

document.addEventListener("DOMContentLoaded", function () {
    const mainImage = document.getElementById("mainProductImage");
    const thumbnails = document.querySelectorAll(".thumb");

    thumbnails.forEach(function (thumb) {
        thumb.addEventListener("click", function () {

            mainImage.src = this.src;

            thumbnails.forEach(t => t.classList.remove("active-thumb"));
            this.classList.add("active-thumb");
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {

    const zoomContainer = document.querySelector(".zoom-container");
    const img = zoomContainer.querySelector("img");

    const zoomLevel = 3; // Increase to 4 for extreme zoom

    zoomContainer.addEventListener("mousemove", function (e) {

        const rect = zoomContainer.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const xPercent = (x / rect.width) * 100;
        const yPercent = (y / rect.height) * 100;

        img.style.transformOrigin = xPercent + "% " + yPercent + "%";
        img.style.transform = "scale(" + zoomLevel + ")";
    });

    zoomContainer.addEventListener("mouseleave", function () {
        img.style.transform = "scale(1)";
        img.style.transformOrigin = "center center";
    });

});
</script>



</body>

</html>
