<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

include('db.php');

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Fetch user details
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);  // Binding the ID as integer
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if the user exists
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('User not found!'); window.location.href='users.php';</script>";
        exit();
    }
}

if (isset($_POST['update_user'])) {
    // Validate and sanitize input
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role = $_POST['role'];

    // Check if the password fields are filled
    if (!empty($_POST['password']) && $_POST['password'] === $_POST['confirm_password']) {
        // Sanitize and hash the password
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password securely
        $query = "UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssi", $username, $password, $role, $user_id);
    } else {
        // Update user without changing the password
        $query = "UPDATE users SET username = ?, role = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssi", $username, $role, $user_id);
    }

    // Execute the update query
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('User updated successfully!'); window.location.href='users.php';</script>";
    } else {
        echo "<script>alert('Error updating user!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - LUXORA Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <?php include('sidebar.php'); ?>

    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Edit User</h1>
        </div>

        <div class="form-container" style="background-color: #161616; padding: 30px; border-radius: 8px; border: 1px solid #333; max-width: 600px;">
            <form method="POST" action="">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label>New Password (leave blank to keep current)</label>
                    <input type="password" name="password">
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" required>
                        <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="user" <?php echo ($user['role'] == 'user') ? 'selected' : ''; ?>>User</option>
                    </select>
                </div>
                <div style="margin-top: 20px;">
                    <button type="submit" name="update_user" class="btn">Update User</button>
                    <a href="user.php" class="btn" style="background: transparent; border: 1px solid var(--gold-primary); color: var(--gold-primary); margin-left: 10px;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>