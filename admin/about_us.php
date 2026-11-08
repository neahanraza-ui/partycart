<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

// Database connection
include('db.php');
include('sidebar.php');

// Fetch About Us content
$about_query = "SELECT * FROM about_us_content";
$about_result = mysqli_query($conn, $about_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us Management - LUXORA Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <?php include('sidebar.php'); ?>

    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">About Us Content Management</h1>
        </div>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div style="background-color: rgba(40, 167, 69, 0.1); color: #28a745; padding: 10px; border-left: 3px solid #28a745; margin-bottom: 20px;">Section updated successfully!</div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
            <div style="background-color: rgba(220, 53, 69, 0.1); color: #dc3545; padding: 10px; border-left: 3px solid #dc3545; margin-bottom: 20px;">Error updating section!</div>
        <?php endif; ?>

        <div class="form-container" style="background-color: #161616; padding: 25px; border-radius: 8px; border: 1px solid #333; margin-bottom: 40px;">
            <h2 style="color: var(--gold-primary); margin-bottom: 20px; font-size: 20px;">Update Sections</h2>
            <?php while ($section = mysqli_fetch_assoc($about_result)): ?>
                <form method="POST" action="update_about_us.php" enctype="multipart/form-data" style="margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 20px;">
                    <h3 style="color: #fff; margin-bottom: 10px; font-size: 16px;">Section: <?php echo htmlspecialchars($section['section_name']); ?></h3>
                    <input type="hidden" name="id" value="<?php echo $section['id']; ?>">
                    <div class="form-group">
                        <input type="text" name="section_name" value="<?php echo htmlspecialchars($section['section_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <textarea name="content" required rows="4"><?php echo htmlspecialchars($section['content']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label style="color: #ccc; margin-bottom: 5px; display: block;">Section Image (Optional)</label>
                        <input type="file" name="image" accept="image/*">
                        <?php if (!empty($section['image_url'])): ?>
                            <p style="font-size: 12px; color: #666; margin-top: 5px;">Current: <?php echo htmlspecialchars(basename($section['image_url'])); ?></p>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn">Update Section</button>
                </form>
            <?php endwhile; ?>
        </div>

        <div class="table-container">
            <h2 style="color: var(--gold-primary); margin-bottom: 20px; font-size: 20px;">Existing Sections</h2>
            <table>
                <thead>
                    <tr>
                        <th>Section Name</th>
                        <th>Content</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    mysqli_data_seek($about_result, 0);
                    while ($section = mysqli_fetch_assoc($about_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($section['section_name']); ?></td>
                            <td><?php echo htmlspecialchars(substr($section['content'], 0, 100)) . '...'; ?></td>
                            <td>
                                <?php if (!empty($section['image_url'])): ?>
                                    <img src="<?php echo $section['image_url']; ?>" width="50" height="50" style="object-fit:cover; border-radius:4px;">
                                <?php else: ?>
                                    <span style="color:#666;">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit_about_us.php?id=<?php echo $section['id']; ?>" class="btn" style="padding: 5px 10px; font-size: 12px; background-color: #444; color: #fff;">Edit</a>
                                <a href="delete_about_us.php?id=<?php echo $section['id']; ?>" class="btn-danger" style="padding: 5px 10px; font-size: 12px; text-decoration: none;">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>