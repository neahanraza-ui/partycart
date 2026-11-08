<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $section_name = $_POST['section_name'];
    $content = $_POST['content'];
    
    // Default to existing update without image change
    $update_image = false;
    $new_image_url = '';

    // Handle File Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $filename = $_FILES['image']['name'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed)) {
            $new_filename = uniqid('about_') . '.' . $file_ext;
            $upload_path = '../uploads/' . $new_filename;
            
            if (!file_exists('../uploads/')) {
                mkdir('../uploads/', 0777, true);
            }

            if (move_uploaded_file($file_tmp, $upload_path)) {
                $new_image_url = 'uploads/' . $new_filename;
                $update_image = true;
            }
        }
    }

    if ($update_image) {
        $stmt = $conn->prepare("UPDATE about_us_content SET section_name=?, content=?, image_url=? WHERE id=?");
        $stmt->bind_param("sssi", $section_name, $content, $new_image_url, $id);
    } else {
        $stmt = $conn->prepare("UPDATE about_us_content SET section_name=?, content=? WHERE id=?");
        $stmt->bind_param("ssi", $section_name, $content, $id);
    }

    if ($stmt->execute()) {
        header('Location: about_us.php?status=success');
    } else {
        header('Location: about_us.php?status=error');
    }
    exit();
} else {
    header('Location: about_us.php');
    exit();
}
?>