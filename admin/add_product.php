<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

include 'db.php';

// Fetch categories
$sql = "SELECT * FROM categories";
$result = $conn->query($sql);
$categories = $result->fetch_all(MYSQLI_ASSOC);

$error_message = '';
$upload_dir = '../uploads/';
$image_urls = array_fill(0, 5, ''); // 5 image slots

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic fields
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];

    // SEO / meta fields with fallback
    $meta_title = !empty($_POST['meta_title']) ? $_POST['meta_title'] : $name . ' | LUXORA';
    $meta_description = !empty($_POST['meta_description']) ? $_POST['meta_description'] : substr(strip_tags($description), 0, 160);
    $meta_keywords = !empty($_POST['meta_keywords']) ? $_POST['meta_keywords'] : $name;
    $slug = !empty($_POST['slug']) ? $_POST['slug'] : strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));

    // Handle up to 5 image uploads
    for ($i = 0; $i < 5; $i++) {
        $file_field = 'image_url_' . ($i + 1); // Match DB column names
        if (isset($_FILES[$file_field]) && $_FILES[$file_field]['error'] == 0) {
            $file_name = $_FILES[$file_field]['name'];
            $file_tmp = $_FILES[$file_field]['tmp_name'];
            $file_size = $_FILES[$file_field]['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (!in_array($file_ext, $allowed)) {
                $error_message = "Invalid file type for image " . ($i + 1);
                break;
            } elseif ($file_size > 5000000) {
                $error_message = "File too large for image " . ($i + 1) . " (max 5MB)";
                break;
            } else {
                if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
                $new_file_name = uniqid('prod_' . ($i + 1) . '_') . '.' . $file_ext;
                $target_file = $upload_dir . $new_file_name;
                if (move_uploaded_file($file_tmp, $target_file)) {
                    $image_urls[$i] = 'uploads/' . $new_file_name;
                } else {
                    $error_message = "Failed to upload image " . ($i + 1);
                    break;
                }
            }
        }
    }

    // Insert into database if no errors
    if (empty($error_message)) {
        // Colors
        $color = !empty($_POST['color']) ? $_POST['color'] : '';

        $sql = "INSERT INTO products 
            (name, description, price, category_id, image_url_1, image_url_2, image_url_3, image_url_4, image_url_5, meta_title, meta_description, meta_keywords, slug, color)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssdissssssssss",
            $name,
            $description,
            $price,
            $category_id,
            $image_urls[0],
            $image_urls[1],
            $image_urls[2],
            $image_urls[3],
            $image_urls[4],
            $meta_title,
            $meta_description,
            $meta_keywords,
            $slug,
            $color
        );

        if ($stmt->execute()) {
            header('Location: dashboard.php');
            exit();
        } else {
            $error_message = "Database error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Product - LUXORA Admin</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>

<?php include('sidebar.php'); ?>

<div class="main-content">
    <div class="page-header">
        <h1 class="page-title">Add New Product</h1>
    </div>

    <div class="form-container" style="background-color: #161616; padding: 30px; border-radius: 8px; border: 1px solid #333; max-width: 800px;">
        <form method="POST" enctype="multipart/form-data">
            <?php if (!empty($error_message)) {
                echo "<p style='color:#dc3545;margin-bottom:20px;background:rgba(220,53,69,0.1);padding:10px;border-left:3px solid #dc3545;'>$error_message</p>";
            } ?>

            <!-- Product Name -->
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" required />
            </div>

            <!-- Price & Category -->
            <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label>Price</label>
                    <input type="number" name="price" step="0.01" required />
                </div>
                <div>
                    <label>Category</label>
                    <select name="category_id" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>">
                                <?php echo $category['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="5" required></textarea>
            </div>

            <!-- Colors -->
            <div class="form-group">
                <label>Available Colors</label>
                <input type="text" name="color" placeholder="e.g. Red, Blue, Black (comma separated)" />
            </div>

            <!-- Product Images (5) -->
            <div class="form-group">
                <label>Product Images (up to 5)</label>
                <?php for ($i = 1; $i <= 5; $i++) { ?>
                    <input type="file" name="image_url_<?php echo $i; ?>" accept="image/*" style="margin-bottom:10px;">
                <?php } ?>
            </div>

            <!-- SEO / Meta Fields -->
            <div class="form-group">
                <label>Meta Title</label>
                <input type="text" name="meta_title" placeholder="SEO title for the product" />
            </div>

            <div class="form-group">
                <label>Meta Description</label>
                <textarea name="meta_description" rows="3" placeholder="SEO description (max 160 characters)"></textarea>
            </div>

            <div class="form-group">
                <label>Meta Keywords</label>
                <input type="text" name="meta_keywords" placeholder="SEO keywords, comma separated" />
            </div>

            <div class="form-group">
                <label>Product Slug</label>
                <input type="text" name="slug" placeholder="SEO-friendly URL slug (optional)" />
            </div>

            <div style="margin-top: 30px;">
                <button type="submit" class="btn">Add Product</button>
                <a href="products.php" class="btn" style="background: transparent; border: 1px solid var(--gold-primary); color: var(--gold-primary); margin-left: 10px;">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
