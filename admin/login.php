<?php
session_start();
include('db.php');

if (isset($_SESSION['admin'])) {
    header('Location: dashboard.php');
    exit();
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Database connection

    // Use prepared statements to avoid SQL injection
    $query = "SELECT * FROM user WHERE username = ? AND role = 'admin'";
    $stmt = mysqli_prepare($conn, $query);

    // Check if statement was prepared successfully
    if (!$stmt) {
        die("Error preparing statement: " . mysqli_error($conn));
    }

    // Bind parameters
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Debugging: Check the number of rows
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        // Debugging: Output the user data
        echo "<pre>";
        print_r($user);
        echo "</pre>";

        // Check if passwords match
        if ($password == $user['password']) {
            $_SESSION['admin'] = $username;
            $_SESSION['admin_id'] = $user['id']; // Required for other admin pages
            header('Location: dashboard.php');
            exit();
        } else {
            echo "<script>alert('Invalid Credentials: Incorrect Password');</script>";
        }
    } else {
        echo "<script>alert('Invalid Credentials: No matching user found');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - LUXORA</title>
    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <div class="login-container">
        <div class="login-form">
            <h2 class="login-title">Admin Login</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" name="login" class="btn" style="width: 100%;">Login</button>
            </form>
            
            <div style="margin-top: 20px; text-align: center;">
                <a href="../index.php" style="color: var(--text-secondary); text-decoration: none; font-size: 14px;">Back to Site</a>
            </div>
        </div>
    </div>
</body>

</html>