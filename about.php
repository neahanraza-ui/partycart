<?php
// Include database connection
include 'includes/db.php';

// Query for fetching meta tags for the 'about' page
include 'includes/meta_helper.php';
$meta_tags = fetch_page_meta($conn, 'about'); 
$og_image = $meta_tags['og_image_url'] ?? 'path_to_default_image.jpg';

// Query for fetching sections for the 'about' page
$sql_about_sections = "SELECT * FROM about_us_content ORDER BY id ASC";  // Fetch content ordered by ID or section order
$result_about_sections = $conn->query($sql_about_sections);

// Check if the query for sections was successful
if ($result_about_sections && $result_about_sections->num_rows > 0) {
  // Fetch sections data as an associative array
  $about_sections = $result_about_sections->fetch_all(MYSQLI_ASSOC);
} else {
  // If no sections are found, define a fallback empty array
  $about_sections = [];
}

// Closing the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($meta_tags['meta_title']); ?></title>

  <!-- Dynamic Meta Tags -->
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
</head>

<body>
  <!-- Navigation -->
  <?php include 'includes/header.php'; ?>


  <!-- About Hero -->
  <section class="about-hero">
    <?php if (isset($about_sections[0])): ?>
      <h1 class="fade-in">
        <?php echo $about_sections[0]['section_name']; ?>
      </h1>
      <p class="fade-in">
        <?php echo $about_sections[0]['content']; ?>
      </p>

    <?php endif; ?>
  </section>

  <!-- About Content -->
  <section class="about-content">
    <?php foreach ($about_sections as $section): ?>
      <div class="about-section fade-in">
        <div class="about-text">
          <h2>
            <?php echo $section['section_name']; ?>
          </h2>
          <p>
            <?php echo $section['content']; ?>
          </p>
        </div>
        <?php if (!empty($section['image_url'])): ?>
          <img src="<?php echo $section['image_url']; ?>" alt="<?php echo $section['section_name']; ?>" class="about-image">
        <?php endif; ?>
      </div>
    <?php endforeach; ?>

    <!-- Our Values Section -->
    <div style="text-align: center; margin: 120px 0 60px;">
      <h2 class="section-title fade-in">Our Values</h2>
      <div class="divider fade-in"></div>
    </div>

    <div class="values-grid">
      <div class="value-card fade-in">
        <i class="fas fa-gem value-icon"></i>
        <h3>Authenticity</h3>
        <p>Every product is guaranteed genuine, sourced directly from verified artisans and designers.</p>
      </div>

      <div class="value-card fade-in">
        <i class="fas fa-award value-icon"></i>
        <h3>Excellence</h3>
        <p>We maintain the highest standards in product selection, customer service, and brand experience.</p>
      </div>

      <div class="value-card fade-in">
        <i class="fas fa-leaf value-icon"></i>
        <h3>Sustainability</h3>
        <p>Committed to ethical practices and environmental responsibility in everything we do.</p>
      </div>

      <div class="value-card fade-in">
        <i class="fas fa-heart value-icon"></i>
        <h3>Passion</h3>
        <p>Driven by love for beautiful craftsmanship and dedication to curating extraordinary pieces.</p>
      </div>

      <div class="value-card fade-in">
        <i class="fas fa-users value-icon"></i>
        <h3>Community</h3>
        <p>Building lasting relationships with our clients, partners, and artisans worldwide.</p>
      </div>

      <div class="value-card fade-in">
        <i class="fas fa-star value-icon"></i>
        <h3>Innovation</h3>
        <p>Embracing new technologies while honoring traditional craftsmanship techniques.</p>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <?php include 'includes/footer.php'; ?>

  <script src="js/products-data.js"></script>
  <script src="js/cart.js"></script>
  <script src="js/animations.js"></script>
</body>

</html>