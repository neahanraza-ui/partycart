<?php
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');

$current_password = trim($_POST['current_password'] ?? "");
$new_password = trim($_POST['new_password'] ?? "");
$confirm_password = trim($_POST['confirm_password'] ?? "");

// Fetch current user
$stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header("Location: user_dashboard.php?error=" . urlencode("User not found"));
    exit();
}

// Update username + email
$stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
$stmt->bind_param("ssi", $username, $email, $user_id);
$stmt->execute();

// Password change only if new password entered
if ($new_password !== '') {

    if ($current_password === '') {
        header("Location: user_dashboard.php?error=" . urlencode("Enter current password"));
        exit();
    }

    // DB password is plain text (your current setup)
    $dbPass = trim((string) $user['password']);

    // Plain text compare (safe compare)
    if (!hash_equals($dbPass, $current_password)) {
        header("Location: user_dashboard.php?error=" . urlencode("Current password incorrect"));
        exit();
    }

    if ($new_password !== $confirm_password) {
        header("Location: user_dashboard.php?error=" . urlencode("Passwords do not match"));
        exit();
    }

    if (strlen($new_password) < 6) {
        header("Location: user_dashboard.php?error=" . urlencode("Password must be at least 6 characters"));
        exit();
    }

    // ✅ Upgrade: store new password HASHED
    $hashed = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed, $user_id);
    $stmt->execute();
}

header("Location: user_dashboard.php?profile_updated=1");
exit();
?>