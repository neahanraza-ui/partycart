<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

include('db.php');

if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $stock_quantity = $_POST['stock_quantity'];
    $sales = $_POST['sales'];
    $featured = isset($_POST['featured']) ? 1 : 0;

    // Create uploads folder if not exists
    if (!file_exists(__DIR__ . '/uploads')) {
        mkdir(__DIR__ . '/uploads', 0777, true);
    }

    $image_urls = [];

    for ($i = 1; $i <= 5; $i++) {
        if (!empty($_FILES["image_url_$i"]["name"])) {

            $tmp = $_FILES["image_url_$i"]["tmp_name"];
            $ext = pathinfo($_FILES["image_url_$i"]["name"], PATHINFO_EXTENSION);
            $new_name = uniqid("product_", true) . "." . $ext;

            // IMPORTANT: save path from PROJECT ROOT
            $path = "admin/uploads/" . $new_name;

            if (move_uploaded_file($tmp, __DIR__ . "/uploads/" . $new_name)) {
                $image_urls["image_url_$i"] = $path;
            }
        }
    }

    $query = "INSERT INTO products 
    (name, description, price, category_id, stock_quantity, sales, featured,
     image_url_1, image_url_2, image_url_3, image_url_4, image_url_5)
    VALUES (
     '$name','$description','$price','$category_id','$stock_quantity','$sales','$featured',
     '" . ($image_urls['image_url_1'] ?? null) . "',
     '" . ($image_urls['image_url_2'] ?? null) . "',
     '" . ($image_urls['image_url_3'] ?? null) . "',
     '" . ($image_urls['image_url_4'] ?? null) . "',
     '" . ($image_urls['image_url_5'] ?? null) . "'
    )";

    mysqli_query($conn, $query);
}

$categories_result = mysqli_query($conn, "SELECT * FROM categories");
$products_result = mysqli_query($conn, "SELECT * FROM products");
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - LUXORA Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <?php include('sidebar.php'); ?>

    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Manage Products</h1>
            <a href="add_product.php" class="btn">Add New Product</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Sales</th>
                        <th>Featured</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
                        <tr>
                            <td>#<?php echo $product['id']; ?></td>
                            <td>
                                <?php if (!empty($product['image_url_1'])): ?>
                                    <img src="<?php echo '../' . $product['image_url_1']; ?>" width="50" height="50" style="object-fit:cover; border-radius:4px;">
                                <?php else: ?>
                                    <span style="color:#666;">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo $product['stock_quantity']; ?></td>
                            <td><?php echo $product['sales']; ?></td>
                            <td><?php echo $product['featured'] ? 'Yes' : 'No'; ?></td>
                            <td>
                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn" style="padding: 5px 10px; font-size: 12px; background-color: #444; color: #fff;">Edit</a>
                                <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="btn-danger" style="padding: 5px 10px; font-size: 12px; text-decoration: none;">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>