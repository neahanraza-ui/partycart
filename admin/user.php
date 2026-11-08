<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

// Database connection
include('db.php');
include('sidebar.php');

// Fetch all users
$query = "SELECT * FROM user";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - LUXORA Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <?php include('sidebar.php'); ?>

    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Manage Admins</h1>
            <a href="add_admin.php" class="btn">Add New Admin</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Admin Username</th>
                        <th>Role</th>
                        <th>Password</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><span style="text-transform: capitalize;"><?php echo htmlspecialchars($user['role']); ?></span></td>
                            <td style="font-family: monospace; color: #ff6b6b;"><?php echo htmlspecialchars($user['password']); ?></td>
                            <td>
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn" style="padding: 5px 10px; font-size: 12px; background-color: #444; color: #fff;">Edit</a>
                                <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn-danger" style="padding: 5px 10px; font-size: 12px; text-decoration: none;" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>