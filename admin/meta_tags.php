<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}
include('db.php');

$message = '';
$error = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $page_name = $_POST['page_name'];
    $meta_title = $_POST['meta_title'];
    $meta_description = $_POST['meta_description'];
    $meta_keywords = $_POST['meta_keywords'];

    // Check if page exists in DB, if not insert, else update
    $check_stmt = $conn->prepare("SELECT id FROM meta_tags WHERE page_name = ?");
    $check_stmt->bind_param("s", $page_name);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Update
        $stmt = $conn->prepare("UPDATE meta_tags SET meta_title = ?, meta_description = ?, meta_keywords = ? WHERE page_name = ?");
        $stmt->bind_param("ssss", $meta_title, $meta_description, $meta_keywords, $page_name);
    } else {
        // Insert (Shouldn't happen often if seeded, but good for robustness)
        $stmt = $conn->prepare("INSERT INTO meta_tags (meta_title, meta_description, meta_keywords, page_name) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $meta_title, $meta_description, $meta_keywords, $page_name);
    }

    if ($stmt->execute()) {
        $message = "Meta tags for '$page_name' updated successfully!";
    } else {
        $error = "Error updating meta tags: " . $conn->error;
    }
}

// Handle Delete Request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $del_stmt = $conn->prepare("DELETE FROM meta_tags WHERE id = ?");
    $del_stmt->bind_param("i", $delete_id);
    
    if ($del_stmt->execute()) {
        $message = "Meta tag deleted successfully!";
    } else {
        $error = "Error deleting meta tag: " . $conn->error;
    }
}

// Fetch all pages data for JS to populate form dynamically
$meta_data = [];
$result = $conn->query("SELECT * FROM meta_tags");
while ($row = $result->fetch_assoc()) {
    $meta_data[$row['page_name']] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Meta Tags - LUXORA Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <?php include('sidebar.php'); ?>

    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Manage Meta Tags</h1>
        </div>

        <?php if ($message): ?>
            <p style="color: #28a745; margin-bottom: 20px;"><?php echo $message; ?></p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p style="color: #dc3545; margin-bottom: 20px;"><?php echo $error; ?></p>
        <?php endif; ?>

        <div class="form-container" style="background-color: #161616; padding: 30px; border-radius: 8px; border: 1px solid #333; max-width: 800px;">
            <form method="POST" action="">
                
                <div class="form-group">
                    <label>Select Page</label>
                    <select name="page_name" id="pageSelect" onchange="loadMetaData()" required>
                        <option value="" disabled selected>-- Select a Page --</option>
                        <option value="index">Home (Index)</option>
                        <option value="about">About Us</option>
                        <option value="classes">Classes/Products</option>
                        <option value="collections">Collections</option>
                        <option value="contact">Contact Us</option>
                        <option value="login">Login</option>
                        <option value="signup">Sign Up</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Meta Title</label>
                    <input type="text" name="meta_title" id="metaTitle" required>
                </div>

                <div class="form-group">
                    <label>Meta Description</label>
                    <textarea name="meta_description" id="metaDescription" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <label>Meta Keywords</label>
                    <textarea name="meta_keywords" id="metaKeywords" rows="3" placeholder="comma, separated, keywords"></textarea>
                </div>

                <button type="submit" class="btn">Update Meta Tags</button>
            </form>
        </div>

        <div class="table-container" style="margin-top: 30px; background-color: #161616; padding: 30px; border-radius: 8px; border: 1px solid #333;">
            <h2 style="margin-bottom: 20px; border-bottom: 1px solid #333; padding-bottom: 10px;">Existing Meta Tags</h2>
            <table class="table" style="width: 100%; border-collapse: collapse; color: #ccc;">
                <thead>
                    <tr style="text-align: left; border-bottom: 1px solid #333;">
                        <th style="padding: 12px; color: var(--gold-primary, #b8962e);">Page</th>
                        <th style="padding: 12px; color: var(--gold-primary, #b8962e);">Title</th>
                        <th style="padding: 12px; color: var(--gold-primary, #b8962e);">Description</th>
                        <th style="padding: 12px; color: var(--gold-primary, #b8962e);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($meta_data)): ?>
                        <tr><td colspan="4" style="padding: 15px; text-align: center;">No meta tags found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($meta_data as $page => $data): ?>
                            <tr style="border-bottom: 1px solid #222;">
                                <td style="padding: 12px;"><?php echo htmlspecialchars(ucfirst($data['page_name'])); ?></td>
                                <td style="padding: 12px;"><?php echo htmlspecialchars($data['meta_title']); ?></td>
                                <td style="padding: 12px;"><?php echo htmlspecialchars(substr($data['meta_description'], 0, 50)) . (strlen($data['meta_description']) > 50 ? '...' : ''); ?></td>
                                <td style="padding: 12px;">
                                    <button onclick="editMeta('<?php echo $data['page_name']; ?>')" class="btn-small" style="background: #b8962e; color: #fff; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; margin-right: 5px;">Edit</button>
                                    <a href="?delete_id=<?php echo $data['id']; ?>" onclick="return confirm('Are you sure you want to delete this meta tag?');" class="btn-small" style="background: #dc3545; color: #fff; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; text-decoration: none; display: inline-block;">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Pass PHP array to JS
        const metaData = <?php echo json_encode($meta_data); ?>;

        function loadMetaData() {
            const page = document.getElementById('pageSelect').value;
            if (metaData[page]) {
                document.getElementById('metaTitle').value = metaData[page].meta_title || '';
                document.getElementById('metaDescription').value = metaData[page].meta_description || '';
                document.getElementById('metaKeywords').value = metaData[page].meta_keywords || '';
            } else {
                // Clear if no data found (or set defaults)
                document.getElementById('metaTitle').value = '';
                document.getElementById('metaDescription').value = '';
                document.getElementById('metaKeywords').value = '';
            }
        }

        function editMeta(pageName) {
            const select = document.getElementById('pageSelect');
            select.value = pageName;
            loadMetaData();
            // Scroll to form
            document.querySelector('.form-container').scrollIntoView({ behavior: 'smooth' });
        }
    </script>
</body>
</html>