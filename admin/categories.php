<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

include('db.php');

// Handle Add Category
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $image_url = ''; // store filename only

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $filename = $_FILES['image']['name'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed)) {
            $new_filename = uniqid('cat_') . '.' . $file_ext;
            $upload_path = '../uploads/' . $new_filename;

            if (!file_exists('../uploads/')) {
                mkdir('../uploads/', 0777, true);
            }

            if (move_uploaded_file($file_tmp, $upload_path)) {
                $image_url = $new_filename;
            } else {
                $error_message = "Failed to move uploaded file.";
            }
        } else {
            $error_message = "Invalid file type. Only JPG, PNG, WEBP, GIF allowed.";
        }
    }

    if (empty($error_message)) {
        $stmt = $conn->prepare("INSERT INTO categories (name, description, image_url) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $description, $image_url);
        if ($stmt->execute()) {
            $success_message = "Category added successfully!";
        } else {
            $error_message = "Database error: " . $stmt->error;
        }
    }
}

// Fetch categories
$categories_query = "SELECT * FROM categories";
$categories_result = mysqli_query($conn, $categories_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - LUXORA Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <?php include('sidebar.php'); ?>

    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Manage Categories</h1>
        </div>

        <?php if (!empty($error_message)): ?>
            <p style="color:#dc3545;"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <p style="color:#28a745;"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <!-- Add Category Form -->
        <div class="form-container"
            style="background-color:#161616; padding:25px; border-radius:8px; border:1px solid #333; margin-bottom:40px;">
            <h2 style="color: var(--gold-primary); margin-bottom:20px; font-size:20px;">Add New Category</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="text" name="name" placeholder="Category Name" required>
                </div>
                <div class="form-group">
                    <textarea name="description" placeholder="Category Description" required></textarea>
                </div>
                <div class="form-group">
                    <label style="color:#ccc; display:block; margin-bottom:5px;">Category Image</label>
                    <input type="file" name="image" accept="image/*">
                </div>
                <button type="submit" name="add_category" class="btn">Add Category</button>
            </form>
        </div>

        <!-- Existing Categories Table -->
        <div class="table-container">
            <h2 style="color: var(--gold-primary); margin-bottom:20px; font-size:20px;">Existing Categories</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                        <tr>
                            <td><?php echo $category['id']; ?></td>
                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                            <td><?php echo htmlspecialchars($category['description']); ?></td>
                            <td>
                                <?php if (!empty($category['image_url'])): ?>
                                    <img src="../uploads/<?php echo $category['image_url']; ?>" width="50" height="50"
                                        style="object-fit:cover; border-radius:4px;">
                                <?php else: ?>
                                    <span style="color:#666;">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit_category.php?id=<?php echo $category['id']; ?>" class="btn"
                                    style="padding:5px 10px; font-size:12px; background-color:#444; color:#fff;">Edit</a>
                                <a href="delete_category.php?id=<?php echo $category['id']; ?>" class="btn-danger"
                                    style="padding:5px 10px; font-size:12px; text-decoration:none;"
                                    onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>