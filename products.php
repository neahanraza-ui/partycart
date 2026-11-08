<?php
include 'includes/db.php';
include 'includes/meta_helper.php';

/* ---------- META TAGS ---------- */
$meta = fetch_page_meta($conn, 'products');

/* ---------- CATEGORIES ---------- */
$sql_categories = "SELECT * FROM categories";
$result_categories = $conn->query($sql_categories);


/* ---------- FILTERS ---------- */
$category_filter = $_GET['category_id'] ?? 'all';
$price_filter = $_GET['price'] ?? 'all';
$sort_filter = $_GET['sort'] ?? 'featured';
$search_query = $_GET['search'] ?? '';


/* ---------- CATEGORY NAME ---------- */
$selected_category_name = 'All Products';

if ($category_filter !== 'all') {
  $cat_res = $conn->query("SELECT name FROM categories WHERE id=" . (int) $category_filter);
  if ($cat_res && $cat_res->num_rows > 0) {
    $selected_category_name = $cat_res->fetch_assoc()['name'];
  }
}

/* ---------- PRODUCTS QUERY ---------- */
$sql_products = "SELECT * FROM products WHERE 1";

/* Search */
if (!empty($search_query)) {
  $search_term = $conn->real_escape_string($search_query);
  $sql_products .= " AND (name LIKE '%$search_term%' OR description LIKE '%$search_term%')";
}


/* Category */
if ($category_filter !== 'all') {
  $sql_products .= " AND category_id = " . (int) $category_filter;
}

/* Price */
$price_ranges = [
  '0-500' => 'price < 500',
  '500-1000' => 'price BETWEEN 500 AND 1000',
  '1000-2000' => 'price BETWEEN 1000 AND 2000',
  '2000-5000' => 'price > 2000'
];

if ($price_filter !== 'all' && isset($price_ranges[$price_filter])) {
  $sql_products .= " AND " . $price_ranges[$price_filter];
}

/* Sorting */
switch ($sort_filter) {
  case 'price-low':
    $sql_products .= " ORDER BY price ASC";
    break;
  case 'price-high':
    $sql_products .= " ORDER BY price DESC";
    break;
  case 'name':
    $sql_products .= " ORDER BY name ASC";
    break;
  default:
    $sql_products .= " ORDER BY featured DESC";
}

$result_products = $conn->query($sql_products);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($meta['meta_title']); ?></title>

<meta name="description" content="<?= htmlspecialchars($meta['meta_description']); ?>">

<meta name="keywords" content="<?= htmlspecialchars($meta['meta_keywords']); ?>">

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
/* ==============================
   PRODUCTS PAGE — COMPACT SIDEBAR
================================ */

body{
  background:#0a0a0a;
    padding-top: 50px;
  color:rgba(243,243,243,0.9);
  font-family:'Inter',sans-serif;
}

/* ================= HEADER ================= */
.products-header{
  background:radial-gradient(circle at top,#111 0%,#000 70%);
  padding:130px 20px 45px;
  text-align:center;
  border-bottom:1px solid rgba(184,150,46,0.15);
}

.section-title{
  font-family:var(--font-luxury);
  font-size:38px;
  letter-spacing:2px;
  text-transform:uppercase;
  color:var(--gold-primary);
  margin:0;
}

.section-subtitle{
  margin-top:8px;
  font-size:14px;
  color:rgba(243,243,243,0.6);
}

.divider{
  width:80px;
  height:2px;
  background:linear-gradient(90deg,transparent,var(--gold-primary),transparent);
  margin:20px auto 0;
}

/* ==============================
   GAME DISCOUNT HERO SECTION
================================ */

.game-discount-section{
  position: relative;
  margin: 30px ;
  margin-top: 40px;
  max-width: 1200px;
  border-radius: 14px;
  overflow: hidden;
  min-height: 260px;

  /* BACKGROUND IMAGE */
  background-image: url("images/istockphoto-1278580953-612x612.jpg"); /* <-- CHANGE IMAGE PATH */
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;

  box-shadow: 0 25px 70px rgba(0,0,0,0.7);
}

/* Dark overlay for readability */
.game-discount-overlay{
  position: absolute;
  inset: 0;
  background: linear-gradient(
    180deg,
    rgba(0,0,0,0.75),
    rgba(0,0,0,0.55)
  );
  backdrop-filter: blur(2px);
  z-index: 1;
}

/* Content on top */
.game-discount-content{
  position: relative;
  z-index: 2;
  height: 100%;
  padding: 32px 28px;

  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;

  text-align: center;
}

/* Title */
.game-discount-content .section-title{
  font-size: 36px;
  margin: 0;
  color: var(--gold-primary);
  letter-spacing: 2px;
}

/* Subtitle */
.game-discount-content .section-subtitle{
  margin-top: 8px;
  font-size: 14px;
  color: rgba(243,243,243,0.75);
  max-width: 520px;
}

/* Divider */
.game-discount-content .divider{
  margin: 18px auto;
}

/* Button */
.game-discount-content .btn-luxury{
  margin-top: 10px;
  padding: 14px 22px;
  font-size: 12px;
  font-weight: 900;
  letter-spacing: 2px;
  border-radius: 2px;
  text-transform: uppercase;
  background: linear-gradient(
    135deg,
    var(--gold-primary),
    var(--gold-secondary)
  );
  color: #000;
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
}

.game-discount-content .btn-luxury:hover{
  transform: translateY(-2px);
  box-shadow: 0 12px 35px rgba(184,150,46,0.4);
  filter: brightness(1.08);
}

/* Responsive */
@media (max-width: 768px){
  .game-discount-section{
    min-height: 220px;
  }

  .game-discount-content .section-title{
    font-size: 30px;
  }
}


.game-discount-content .btn-luxury:hover{
  transform:translateY(-2px);
  box-shadow:0 10px 25px rgba(184,150,46,0.35);
}

/* ================= FILTERS ================= */
.filters-section{
  padding:18px 20px;
}

.filters-section-form{
  max-width:1200px;
  margin:0 auto;
}

.filters-container{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:8px;
  padding:10px;
  background:rgba(10,10,10,0.85);
  backdrop-filter:blur(18px);
  border:1px solid rgba(184,150,46,0.2);
  border-radius:10px;
  box-shadow:0 14px 40px rgba(0,0,0,0.55);
}

.filter-select{
  padding:11px 12px;
  font-size:13px;
  background:rgba(0,0,0,0.35);
  border:1px solid rgba(184,150,46,0.35);
  color:#eee;
  border-radius:2px;
  cursor:pointer;
}

.filter-select:focus{
  outline:none;
  border-color:var(--gold-primary);
  box-shadow:0 0 0 2px rgba(184,150,46,0.15);
}

/* ================= GRID ================= */
.products-grid.section{
  padding:18px 20px 65px;
}

#productsGrid{
  max-width:1200px;
  margin:0 auto;
}

.grid-4{
  display:grid;
  grid-template-columns:repeat(4,1fr);
  gap:12px;
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
  transition:all .3s ease;
}

/* Sidebar hover slide */
.product-card:hover{
  background:rgba(184,150,46,0.03);
  padding-left:8px;
  padding-right:8px;
  margin-left:-8px;
  margin-right:-8px;
  border-color:rgba(184,150,46,0.25);
}

/* Image */
.product-card img{
  width:100%;
  height:165px;
  object-fit:cover;
  transition:transform .5s ease;
}

.product-card:hover img{
  transform:scale(1.06);
}

/* Text */
.product-card h3{
  margin:10px 12px 4px;
  font-family:var(--font-luxury);
  font-size:15px;
  font-weight:500;
  color:#fff;
}

.product-card p{
  margin:0 12px 6px;
  font-size:12.5px;
  line-height:1.35;
  color:rgba(243,243,243,0.7);
}

/* Price */
.product-card p:last-of-type{
  color:var(--gold-primary);
  font-weight:700;
  font-size:14px;
  margin-top:auto;
}

/* ================= BUTTONS ================= */
.product-card-buttons{
  display:flex;
  gap:8px;
  padding:10px 12px 12px;
}

.btn-view{
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
}

.btn-view:hover{
  background:rgba(184,150,46,0.08);
  transform:translateY(-2px);
}

.btn-view::after{
  content:'\f105';
  font-family:'Font Awesome 6 Free';
  font-weight:900;
}

/* Add to cart */
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
}

.btn-add-to-cart:hover{
  transform:translateY(-2px);
  box-shadow:0 10px 25px rgba(184,150,46,0.35);
}

/* ================= RESPONSIVE ================= */
@media(max-width:1100px){
  .grid-4{grid-template-columns:repeat(3,1fr);}
}

@media(max-width:820px){
  .filters-container{grid-template-columns:1fr;}
  .grid-4{grid-template-columns:repeat(2,1fr);}
}

@media(max-width:480px){
  .grid-4{grid-template-columns:1fr;}
}


  </style>
</head>
<body>
  <?php include 'includes/header.php'; ?>
</section>

<!-- Game and Discount Section -->
<section class="game-discount-section">
  <div class="game-discount-overlay"></div>

  <div class="game-discount-content">
    <h1 class="section-title">Play to Unlock Luxury</h1>
    <p class="section-subtitle">
      Complete the maze to claim an exclusive discount code
    </p>
    <div class="divider"></div>

    <button class="btn-luxury"
      onclick="window.location.href='game/Maze-Game-main/index.php'">
      Play Game
    </button>
  </div>
</section>

 <!-- Filters Section -->
<section class="filters-section">
  <!-- FILTERS -->
  <form method="GET" class="filters-section-form">
    <div class="filters-container">
      
      <!-- SEARCH INPUT -->
      <div class="filter-search-wrapper" style="display:flex; align-items:center; background:rgba(0,0,0,0.35); border:1px solid rgba(184,150,46,0.35); border-radius:2px; padding:0 10px;">
         <i class="fas fa-search" style="color:rgba(184,150,46,0.5); font-size:12px; margin-right:8px;"></i>
         <input type="text" name="search" value="<?= htmlspecialchars($search_query); ?>" placeholder="Search products..." style="background:transparent; border:none; color:#eee; padding:11px 0; font-size:13px; outline:none; width:100%;">
      </div>


      <!-- CATEGORY -->
      <select name="category_id" class="filter-select" onchange="this.form.submit()">
        <option value="all">All Categories</option>
        <?php while ($category = $result_categories->fetch_assoc()): ?>
          <option value="<?= $category['id']; ?>" <?= ($category_filter == $category['id']) ? 'selected' : ''; ?>>
            <?= htmlspecialchars($category['name']); ?>
          </option>
        <?php endwhile; ?>
      </select>

      <!-- PRICE -->
      <select name="price" class="filter-select" onchange="this.form.submit()">
        <option value="all">All Prices</option>
        <option value="0-500" <?= $price_filter == '0-500' ? 'selected' : ''; ?>>Under $500</option>
        <option value="500-1000" <?= $price_filter == '500-1000' ? 'selected' : ''; ?>>$500 – $1,000</option>
        <option value="1000-2000" <?= $price_filter == '1000-2000' ? 'selected' : ''; ?>>$1,000 – $2,000</option>
        <option value="2000-5000" <?= $price_filter == '2000-5000' ? 'selected' : ''; ?>>$2,000+</option>
      </select>

      <!-- SORT -->
      <select name="sort" class="filter-select" onchange="this.form.submit()">
        <option value="featured" <?= $sort_filter == 'featured' ? 'selected' : ''; ?>>Featured</option>
        <option value="price-low" <?= $sort_filter == 'price-low' ? 'selected' : ''; ?>>Price: Low → High</option>
        <option value="price-high" <?= $sort_filter == 'price-high' ? 'selected' : ''; ?>>Price: High → Low</option>
        <option value="name" <?= $sort_filter == 'name' ? 'selected' : ''; ?>>Name A–Z</option>
      </select>

    </div>
  </form>
</section>


  <!-- Products Grid -->
<section class="products-grid section">
  <div class="grid-4" id="productsGrid">

    <?php if ($result_products && $result_products->num_rows > 0): ?>
      <?php while ($product = $result_products->fetch_assoc()): ?>

        <?php
        // Pick the first available product image
        $image = 'uploads/no-image.png';
        for ($i = 1; $i <= 5; $i++) {
          if (!empty($product["image_url_$i"])) {
            $image = $product["image_url_$i"];
            break;
          }
        }
        ?>

        <div class="product-card">

          <!-- Product Image -->
          <img src="<?php echo htmlspecialchars($image); ?>"
            alt="<?php echo htmlspecialchars($product['name'] ?? 'Product'); ?>">

          <!-- Product Name -->
          <h3><?php echo htmlspecialchars($product['name'] ?? ''); ?></h3>

          <!-- Product Description -->
          <p><?php echo htmlspecialchars($product['description'] ?? ''); ?></p>

          <!-- Product Price -->
          <p>
            $<?php echo isset($product['price']) ? number_format((float) $product['price'], 2) : '0.00'; ?>
          </p>
 <!-- View Button -->
<div class="product-card-buttons">
  <a href="product-details.php?id=<?php echo (int) $product['id']; ?>" class="btn-view">
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

      <?php endwhile; ?>
    <?php else: ?>
      <p>No products found.</p>
    <?php endif; ?>

  </div>
</section>




  <!-- Footer -->
  <?php include 'includes/footer.php'; ?>

  <!-- <script src="js/products-data.js"></script> -->
  <script src="js/animations.js"></script>
  <!-- <script src="js/products.js"></script> -->
  </script>

</body>

</html>
