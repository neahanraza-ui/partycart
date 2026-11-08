
<?php
// Database connection
include 'includes/db.php';

// Query for fetching meta tags for the 'collections' page
include 'includes/meta_helper.php';



$meta = fetch_page_meta($conn, 'collections');

// Fallback values (VERY IMPORTANT)
$meta_title       = $meta['meta_title'] ?? 'Collections';
$meta_description = $meta['meta_description'] ?? '';
$meta_keywords    = $meta['meta_keywords'] ?? '';
$og_image         = $meta['og_image_url'] ?? '';

$sql_categories = "SELECT * FROM categories";
$result_categories = $conn->query($sql_categories);
// Closing the connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($meta_title) ?></title>

<meta name="description" content="<?= htmlspecialchars($meta_description) ?>">
<meta name="keywords" content="<?= htmlspecialchars($meta_keywords) ?>">

<meta property="og:title" content="<?= htmlspecialchars($meta_title) ?>">
<meta property="og:description" content="<?= htmlspecialchars($meta_description) ?>">

<?php if (!empty($og_image)) : ?>
<meta property="og:image" content="<?= htmlspecialchars($og_image) ?>">
<?php endif; ?>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="styles/pages.css">
  <link rel="stylesheet" href="styles/fonts.css">
  <link rel="stylesheet" href="styles/luxury-theme.css">
  <link rel="stylesheet" href="styles/tailwind.css">
  <link rel="stylesheet" href="styles/theme.css">
  <style>
  /* ===============================
   COLLECTIONS PAGE — REDESIGN
================================ */

.collections-page{
  padding:40px 20px 80px;
  background:#0b0b0b;
}

/* ================= GRID ================= */
.collections-grid{
  max-width:1200px;
  margin:0 auto;
  display:grid;
  grid-template-columns:repeat(auto-fill, minmax(240px, 1fr));
  gap:20px;
}

/* ================= CARD ================= */
.collection-card{
  position:relative;
  border-radius:14px;
  overflow:hidden;
  background:#111;
  border:1px solid rgba(255,255,255,0.06);
  cursor:pointer;

  transition:
    transform .45s cubic-bezier(.22,1,.36,1),
    box-shadow .45s ease,
    border-color .3s ease;
}

.collection-card:hover{
  transform:translateY(-10px);
  border-color:rgba(184,150,46,.4);
  box-shadow:
    0 25px 60px rgba(0,0,0,.7),
    inset 0 0 0 1px rgba(184,150,46,.15);
}

/* ================= IMAGE ================= */
.collection-image{
  width:100%;
  height:190px;
  object-fit:cover;
  display:block;
  filter:brightness(.9);
  transition:
    transform .6s cubic-bezier(.22,1,.36,1),
    filter .6s ease;
}

.collection-card:hover .collection-image{
  transform:scale(1.08);
  filter:brightness(1);
}

/* Gradient overlay for readability */
.collection-card::before{
  content:'';
  position:absolute;
  inset:0;
  background:linear-gradient(
    to top,
    rgba(0,0,0,.75),
    rgba(0,0,0,.2) 45%,
    transparent
  );
  pointer-events:none;
}

/* ================= CONTENT ================= */
.collection-overlay{
  position:absolute;
  bottom:0;
  left:0;
  right:0;
  padding:16px 16px 18px;
  z-index:2;
}

/* ================= TEXT ================= */
.collection-title{
  margin:0 0 6px;
  font-family:var(--font-luxury);
  font-size:16px;
  font-weight:600;
  letter-spacing:.4px;
  color:#fff;
}

.collection-description{
  margin:0;
  font-size:13px;
  line-height:1.4;
  color:rgba(255,255,255,.75);

  display:-webkit-box;
  -webkit-line-clamp:2;
  -webkit-box-orient:vertical;
  overflow:hidden;
}

/* ================= ARROW ================= */
.collection-card::after{
  content:'\f105';
  font-family:'Font Awesome 6 Free';
  font-weight:900;

  position:absolute;
  bottom:18px;
  right:18px;

  width:36px;
  height:36px;
  border-radius:50%;
  display:grid;
  place-items:center;

  background:rgba(184,150,46,.9);
  color:#000;

  transform:translateX(-8px);
  opacity:0;

  transition:
    transform .4s cubic-bezier(.22,1,.36,1),
    opacity .3s ease;
}

.collection-card:hover::after{
  transform:translateX(0);
  opacity:1;
}

/* ================= EMPTY STATE ================= */
.collections-page p{
  max-width:1200px;
  margin:40px auto 0;
  text-align:center;
  color:rgba(255,255,255,.6);
}

/* ================= RESPONSIVE ================= */

/* Tablets */
@media (max-width:900px){
  .collection-image{
    height:210px;
  }
}

/* Mobile */
@media (max-width:520px){
  .collections-grid{
    grid-template-columns:1fr;
  }

  .collection-image{
    height:230px;
  }
}

/* Touch devices */
@media (hover:none){
  .collection-card:hover{
    transform:none;
    box-shadow:none;
  }

  .collection-card:hover .collection-image{
    transform:none;
    filter:brightness(.95);
  }

  .collection-card:hover::after{
    opacity:1;
    transform:none;
  }
}

</style>
</head>

<body>
  <!-- Navigation -->
  <?php include 'includes/header.php'; ?>

  <!-- Collections Header -->
  <section class="products-header">
    <h1 class="section-title fade-in">Curated Collections</h1>
    <p class="section-subtitle fade-in">Explore our handpicked luxury selections</p>
    <div class="divider fade-in"></div>
  </section>

  <!-- Collections Grid -->
  <section class="collections-page">
    <div class="collections-grid" id="collectionsGrid">
      <?php if ($result_categories && $result_categories->num_rows > 0): ?>
        <?php while ($category = $result_categories->fetch_assoc()): ?>
          <?php
          $image = !empty($category['image_url']) ? $category['image_url'] : 'images/no-image.png';
          ?>
          <div class="collection-card" onclick="window.location.href='products.php?category_id=<?php echo $category['id']; ?>'">
            <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" class="collection-image">
            <div class="collection-overlay">
              <h3 class="collection-title"><?php echo htmlspecialchars($category['name']); ?></h3>
              <p class="collection-description"><?php echo htmlspecialchars($category['description']); ?></p>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No collections found.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- Footer -->
  <?php include 'includes/footer.php'; ?>

  <script src="js/products-data.js"></script>
  <script src="js/cart.js"></script>
  <script src="js/animations.js"></script>
</body>

</html>
