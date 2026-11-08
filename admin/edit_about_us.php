<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

// Database connection
include('db.php');

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
<style>
    body { background: #0a0a0a; color: #f3f3f3; font-family: 'Inter', sans-serif; }
    .main-content { padding: 30px; }
    .page-header h1 { color: #d4af37; margin-bottom: 20px; }
    .form-container { background: #161616; padding: 25px; border-radius: 8px; border: 1px solid #333; margin-bottom: 40px; }
    .form-container h2 { color: #d4af37; margin-bottom: 20px; font-size: 20px; }
    form { margin-bottom: 30px; border-bottom: 1px solid #333; padding-bottom: 20px; }
    h3 { color: #fff; margin-bottom: 10px; font-size: 16px; }
    .form-group { margin-bottom: 15px; }
    input[type="text"], textarea, input[type="file"] { width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #444; background: #1a1a1a; color: #f3f3f3; }
    textarea { resize: vertical; }
    .btn { background: linear-gradient(135deg, #d4af37, #b59c4c); color: #000; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
    .btn:hover { filter: brightness(1.1); }
    table { width: 100%; border-collapse: collapse; background: #1a1a1a; border-radius: 8px; overflow: hidden; }
    th, td { padding: 12px; border-bottom: 1px solid #333; }
    th { background: #2a2a2a; color: #d4af37; text-align: left; }
    td { color: #f3f3f3; }
    tr:hover { background: #252525; }
    img { border-radius: 4px; object-fit: cover; }
    .remove-checkbox { display: flex; align-items: center; gap: 5px; color: #f3f3f3; font-size: 14px; }
</style>
</head>
<body>

<div class="main-content">
    <div class="page-header">
        <h1>About Us Content Management</h1>
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div style="background-color: rgba(40, 167, 69, 0.1); color: #28a745; padding: 10px; border-left: 3px solid #28a745; margin-bottom: 20px;">Section updated successfully!</div>
    <?php elseif (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
            <div style="background-color: rgba(220, 53, 69, 0.1); color: #dc3545; padding: 10px; border-left: 3px solid #dc3545; margin-bottom: 20px;">Error updating section!</div>
    <?php endif; ?>

    <div class="form-container">
        <h2>Update Sections</h2>
        <?php while ($section = mysqli_fetch_assoc($about_result)): ?>
                <form method="POST" action="update_about_us.php" enctype="multipart/form-data">
                    <h3>Section: <?php echo htmlspecialchars($section['section_name']); ?></h3>
                    <input type="hidden" name="id" value="<?php echo $section['id']; ?>">

                    <div class="form-group">
                        <label>Section Name</label>
                        <input type="text" name="section_name" value="<?php echo htmlspecialchars($section['section_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Content</label>
                        <textarea name="content" required rows="4"><?php echo htmlspecialchars($section['content']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Section Image (Optional)</label>
                        <input type="file" name="image" accept="image/*">
                        <?php if (!empty($section['image_url'])): ?>
                                <p style="font-size:12px; color:#ccc; margin-top:5px;">Current: <?php echo htmlspecialchars(basename($section['image_url'])); ?></p>
                                <label class="remove-checkbox">
                                    <input type="checkbox" name="remove_image" value="1"> Remove current image
                                </label>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn">Update Section</button>
                </form>
        <?php endwhile; ?>
    </div>

    <div class="table-container">
        <h2>Existing Sections</h2>
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
                                        <img src="<?php echo $section['image_url']; ?>" width="50" height="50">
                                <?php else: ?>
                                        <span style="color:#666;">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit_about_us.php?id=<?php echo $section['id']; ?>" class="btn" style="padding: 5px 10px; font-size: 12px;">Edit</a>
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
