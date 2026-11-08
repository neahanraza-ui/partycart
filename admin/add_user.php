<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['add_user'])) {
    // Database connection
    include('db.php');

    // Sanitize and collect form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = $_POST['role'];

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert the new user into the database
    $query = "INSERT INTO user (username, email, password, role, created_at) 
              VALUES ('$username', '$email', '$hashed_password', '$role', NOW())";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('User added successfully!'); window.location.href='users.php';</script>";
    } else {
        echo "<script>alert('Error adding user!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User - LUXORA Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <?php include('sidebar.php'); ?>

    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Add New User</h1>
        </div>

        <div class="form-container" style="background-color: #161616; padding: 30px; border-radius: 8px; border: 1px solid #333; max-width: 600px;">
            <form method="POST" action="">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div style="margin-top: 20px;">
                    <button type="submit" name="add_user" class="btn">Add User</button>
                    <a href="user.php" class="btn" style="background: transparent; border: 1px solid var(--gold-primary); color: var(--gold-primary); margin-left: 10px;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>