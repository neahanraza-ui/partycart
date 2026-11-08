<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

// Database connection
include('db.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete the About Us section
    $query = "DELETE FROM about_us_content WHERE id='$id'";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('About Us section deleted successfully'); window.location.href='about_us.php';</script>";
    } else {
        echo "<script>alert('Error deleting section');</script>";
    }
}
?>