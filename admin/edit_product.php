<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

include('db.php');

// Fetch product details
if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $query = "SELECT * FROM products WHERE id = $product_id";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $product = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Product not found!'); window.location.href='products.php';</script>";
        exit();
    }
}

// Update product
if (isset($_POST['update_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);

    // SEO / meta fields
    $meta_title = !empty($_POST['meta_title']) ? mysqli_real_escape_string($conn, $_POST['meta_title']) : $name . ' | LUXORA';
    $meta_description = !empty($_POST['meta_description']) ? mysqli_real_escape_string($conn, $_POST['meta_description']) : substr(strip_tags($description), 0, 160);
    $meta_keywords = !empty($_POST['meta_keywords']) ? mysqli_real_escape_string($conn, $_POST['meta_keywords']) : $name;
    $slug = !empty($_POST['slug']) ? mysqli_real_escape_string($conn, $_POST['slug']) : strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));

    $upload_dir = '../uploads/';          // filesystem path
    $upload_url_prefix = 'uploads/';       // URL stored in DB

    for ($i = 1; $i <= 5; $i++) {
        if (isset($_POST['remove_image_' . $i]) && $_POST['remove_image_' . $i] == 1) {
            $image_urls[] = ''; // remove image
        } elseif (isset($_FILES['image_url_' . $i]) && $_FILES['image_url_' . $i]['error'] == 0) {
            $file_tmp = $_FILES['image_url_' . $i]['tmp_name'];
            $file_ext = strtolower(pathinfo($_FILES['image_url_' . $i]['name'], PATHINFO_EXTENSION));
            $new_file_name = uniqid('prod_' . $i . '_') . '.' . $file_ext;
            $target_file = $upload_dir . $new_file_name;

            if (!file_exists($upload_dir))
                mkdir($upload_dir, 0777, true);

            if (move_uploaded_file($file_tmp, $target_file)) {
                $image_urls[] = $upload_url_prefix . $new_file_name; // store relative URL
            } else {
                $image_urls[] = $product['image_url_' . $i]; // fallback
            }
        } else {
            $image_urls[] = $product['image_url_' . $i]; // keep existing
        }
    }

    $color = isset($_POST['color']) ? mysqli_real_escape_string($conn, $_POST['color']) : '';

    // Update product in database
    $query = "UPDATE products SET 
                name='$name',
                description='$description',
                price='$price',
                color='$color',
                category_id='$category_id',
                image_url_1='{$image_urls[0]}',
                image_url_2='{$image_urls[1]}',
                image_url_3='{$image_urls[2]}',
                image_url_4='{$image_urls[3]}',
                image_url_5='{$image_urls[4]}',
                meta_title='$meta_title',
                meta_description='$meta_description',
                meta_keywords='$meta_keywords',
                slug='$slug'
              WHERE id='$product_id'";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Product updated successfully!'); window.location.href='products.php';</script>";
    } else {
        echo "<script>alert('Error updating product: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Product - LUXORA Admin</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>
<?php include('sidebar.php'); ?>

<div class="main-content">
    <div class="page-header">
        <h1 class="page-title">Edit Product</h1>
    </div>

    <div class="form-container" style="background-color: #161616; padding: 25px; border-radius: 8px; border: 1px solid #333; max-width: 800px;">
        <form method="POST" enctype="multipart/form-data">

            <!-- Product Name & Description -->
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="5" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Available Colors</label>
                <input type="text" name="color" value="<?php echo htmlspecialchars($product['color'] ?? ''); ?>" placeholder="e.g. Red, Blue, Black (comma separated)">
            </div>

            <!-- Price & Category -->
            <div class="form-group" style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div>
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required>
                </div>
                <div>
                    <label>Category</label>
                    <select name="category_id" required>
                        <?php
                        $categories_query = "SELECT * FROM categories";
                        $categories_result = mysqli_query($conn, $categories_query);
                        while ($category = mysqli_fetch_assoc($categories_result)) {
                            $selected = ($category['id'] == $product['category_id']) ? 'selected' : '';
                            echo "<option value='{$category['id']}' $selected>{$category['name']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- SEO / Meta Fields -->
            <div class="form-group">
                <label>Meta Title</label>
                <input type="text" name="meta_title" value="<?php echo htmlspecialchars($product['meta_title']); ?>">
            </div>
            <div class="form-group">
                <label>Meta Description</label>
                <textarea name="meta_description" rows="3"><?php echo htmlspecialchars($product['meta_description']); ?></textarea>
            </div>
            <div class="form-group">
                <label>Meta Keywords</label>
                <input type="text" name="meta_keywords" value="<?php echo htmlspecialchars($product['meta_keywords']); ?>">
            </div>
<div class="form-group">
    <label>Slug</label>
    <input type="text" name="slug" value="<?php echo isset($product['slug']) ? htmlspecialchars($product['slug']) : ''; ?>">
            </div>

            <!-- Images (5 max) -->
            <div class="form-group">
                <label>Product Images</label>
                <?php for ($i = 1; $i <= 5; $i++) { ?>
                    <div style="margin-bottom:10px; border:1px solid #333; padding:10px;">
                        <label>Image <?php echo $i; ?></label>
                        <input type="file" name="image_url_<?php echo $i; ?>" accept="image/*">
                        <?php if (!empty($product['image_url_' . $i])) { ?>
                            <div style="margin-top:5px; display:flex; align-items:center;">
<img src="../<?php echo htmlspecialchars($product['image_url_' . $i]); ?>" width="50" height="50" style="margin-right:10px;">
                                <label style="font-size:12px; color:#666;">
                                    <input type="checkbox" name="remove_image_<?php echo $i; ?>" value="1"> Remove
                                </label>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>

            <!-- Submit -->
            <div style="margin-top:20px;">
                <button type="submit" name="update_product" class="btn">Update Product</button>
                <a href="products.php" class="btn" style="background: transparent; border:1px solid var(--gold-primary); color: var(--gold-primary); margin-left:10px;">Cancel</a>
            </div>

        </form>
    </div>
</div>
</body>
</html>
