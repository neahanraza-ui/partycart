<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

// Database connection
include('db.php');

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Delete the user
    $query = "DELETE FROM user WHERE id='$user_id'";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('User deleted successfully'); window.location.href='users.php';</script>";
    } else {
        echo "<script>alert('Error deleting user!'); window.location.href='users.php';</script>";
    }
}
?>