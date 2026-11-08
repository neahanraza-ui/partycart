<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

include('db.php');

if (!isset($_GET['id'])) {
    header('Location: categories.php');
    exit();
}

$category_id = intval($_GET['id']);

// Get image filename to delete
$result = mysqli_query($conn, "SELECT image_url FROM categories WHERE id = $category_id");
if ($row = mysqli_fetch_assoc($result)) {
    $image_file = '../uploads/' . $row['image_url'];
    if (file_exists($image_file) && !empty($row['image_url'])) {
        unlink($image_file); // delete the image file
    }
}

// Delete category from DB
mysqli_query($conn, "DELETE FROM categories WHERE id = $category_id");

header('Location: categories.php');
exit();
?>