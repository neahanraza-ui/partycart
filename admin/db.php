<?php
// db.php - Database connection

// Set session lifetime to 30 days (in seconds)
$session_lifetime = 30 * 24 * 60 * 60;

if (session_status() === PHP_SESSION_NONE) {
    // Force session to persist across browser restarts
    ini_set('session.gc_maxlifetime', $session_lifetime);
    ini_set('session.cookie_lifetime', $session_lifetime);

    // Explicitly set cookie parameters for all paths and same-site policy
    session_set_cookie_params([
        'lifetime' => $session_lifetime,
        'path' => '/',
        'domain' => '', // Current domain
        'secure' => false, // Set to true if using HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}

$servername = "localhost"; // Your database server (usually 'localhost')
$username = "root"; // Your database username (default for XAMPP is 'root')
$password = ""; // Your database password (default for XAMPP is empty)
$dbname = 'luxora_db';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
