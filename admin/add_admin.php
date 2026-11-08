<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

include('db.php');

$error = '';
$success = '';

if (isset($_POST['add_admin'])) {
    // Sanitize and collect form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = $_POST['role'];

    // Validation
    if(empty($username) || empty($password)){
        $error = "Username and Password are required.";
    } else {
        // Basic check if username exists
        $check = mysqli_query($conn, "SELECT * FROM user WHERE username = '$username'");
        if(mysqli_num_rows($check) > 0){
            $error = "Username already exists.";
        } else {
            // Insert into USER table (Admin table) with PLAIN TEXT password
            // Removed email as it might not be in the table structure
            $query = "INSERT INTO user (username, password, role, created_at) 
                      VALUES ('$username', '$password', '$role', NOW())";
            
            if (mysqli_query($conn, $query)) {
                $success = "Admin added successfully!";
            } else {
                $error = "Error adding admin: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Admin - LUXORA Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <?php include('sidebar.php'); ?>

    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Add New Admin</h1>
        </div>

        <div class="form-container" style="background-color: #161616; padding: 30px; border-radius: 8px; border: 1px solid #333; max-width: 600px;">
            <?php if($error): ?>
                <div style="background:rgba(220,53,69,0.1); border:1px solid #dc3545; color:#dc3545; padding:10px; margin-bottom:15px; border-radius:4px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <?php if($success): ?>
                <div style="background:rgba(40,167,69,0.1); border:1px solid #28a745; color:#28a745; padding:10px; margin-bottom:15px; border-radius:4px;">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>
                <!-- Email field removed as 'user' table does not support it -->
                <div class="form-group">
                    <label>Password</label>
                    <input type="text" name="password" required placeholder="Enter plain text password">
                    <p style="font-size:12px; color:#666; margin-top:5px;">Password will be stored unhashed.</p>
                </div>
                <div class="form-group">
                    <label>Access Level (Role)</label>
                    <select name="role" required>
                        <option value="admin">Admin</option>
                        <option value="super_admin">Super Admin</option>
                        <option value="manager">Manager</option>
                    </select>
                </div>
                <div style="margin-top: 20px;">
                    <button type="submit" name="add_admin" class="btn">Add Admin</button>
                    <a href="user.php" class="btn" style="background: transparent; border: 1px solid var(--gold-primary); color: var(--gold-primary); margin-left: 10px;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
