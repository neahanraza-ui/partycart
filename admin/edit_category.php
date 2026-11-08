<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

include('db.php');

$error_message = '';
$success_message = '';

if (isset($_GET['id'])) {
    $category_id = intval($_GET['id']);
    
    // Fetch Category
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();

    if (!$category) {
        header('Location: categories.php');
        exit();
    }
} else {
    header('Location: categories.php');
    exit();
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $current_image = $category['image_url'];
    $new_image_url = $current_image;

    // Handle File Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $filename = $_FILES['image']['name'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed)) {
            $new_filename = uniqid('cat_') . '.' . $file_ext;
            $upload_path = '../uploads/' . $new_filename;
            
            // Ensure uploads directory exists
            if (!file_exists('../uploads/')) {
                mkdir('../uploads/', 0777, true);
            }

            if (move_uploaded_file($file_tmp, $upload_path)) {
                $new_image_url = 'uploads/' . $new_filename;
            } else {
                $error_message = "Failed to upload image.";
            }
        } else {
            $error_message = "Invalid file type.";
        }
    }

    if (empty($error_message)) {
        $update_stmt = $conn->prepare("UPDATE categories SET name = ?, description = ?, image_url = ? WHERE id = ?");
        $update_stmt->bind_param("sssi", $name, $description, $new_image_url, $category_id);
        
        if ($update_stmt->execute()) {
            $success_message = "Category updated successfully!";
            // Refresh category data
            $stmt->execute();
            $result = $stmt->get_result();
            $category = $result->fetch_assoc();
        } else {
            $error_message = "Database error.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category - LUXORA Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <?php include('sidebar.php'); ?>

    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Edit Category</h1>
        </div>

        <div class="form-container" style="background-color: #161616; padding: 30px; border-radius: 8px; border: 1px solid #333; max-width: 600px;">
            <?php if ($error_message): ?>
                <div style="background-color: rgba(220, 53, 69, 0.1); color: #dc3545; padding: 10px; margin-bottom: 20px; border-left: 3px solid #dc3545;">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div style="background-color: rgba(40, 167, 69, 0.1); color: #28a745; padding: 10px; margin-bottom: 20px; border-left: 3px solid #28a745;">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Category Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4" required><?php echo htmlspecialchars($category['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Current Image</label>
                    <?php if (!empty($category['image_url'])): ?>
                        <div style="margin-bottom: 10px;">
                            <img src="<?php echo '../' . $category['image_url']; // Assuming relative to root ?>" alt="Current Image" style="max-width: 100px; border-radius: 4px;">
                            <!-- Note: image_url in DB is usually 'uploads/...' so '../' is needed if admin is in /admin/ -->
                        </div>
                    <?php else: ?>
                        <p style="color: #666;">No image set.</p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Upload New Image (Optional)</label>
                    <input type="file" name="image" accept="image/*">
                </div>

                <div style="margin-top: 30px;">
                    <button type="submit" class="btn">Update Category</button>
                    <a href="categories.php" class="btn" style="background: transparent; border: 1px solid var(--gold-primary); color: var(--gold-primary); margin-left: 10px;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
