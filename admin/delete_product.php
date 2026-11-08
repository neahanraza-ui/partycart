<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

include('db.php');

// Get Product ID from URL
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch product details (to delete the images if necessary)
    $query = "SELECT * FROM products WHERE id = '$product_id'";
    $result = mysqli_query($conn, $query);
    $product = mysqli_fetch_assoc($result);

    // Delete product images from server (optional)
    $image_urls = explode(",", $product['image_urls']);
    foreach ($image_urls as $image) {
        if (file_exists($image)) {
            unlink($image);  // Delete the image file
        }
    }

    // Delete product from the database
    $query = "DELETE FROM products WHERE id = '$product_id'";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Product deleted successfully!'); window.location.href='products.php';</script>";
    } else {
        echo "<script>alert('Error deleting product!');</script>";
    }
}
?>