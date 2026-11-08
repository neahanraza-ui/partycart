<?php
// Set error reporting to log to a file instead of outputting to break JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '../../php_error.log');

header('Content-Type: application/json');

try {
    include '../../includes/db.php';
    if (!$conn) {
        throw new Exception("Database connection failed.");
    }

    // Enable mysqli exceptions
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    // Get POST data
    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);

    if (!isset($data['email']) || !isset($data['difficulty_val'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request data.']);
        exit;
    }

    $email = $data['email'];
    $diff_val = $data['difficulty_val'];

    // Map difficulty value back to name
    $diff_map = [
        "10" => "Easy",
        "15" => "Medium",
        "25" => "Hard",
        "38" => "Extreme"
    ];

    if (!isset($diff_map[$diff_val])) {
        echo json_encode(['success' => false, 'message' => 'Invalid difficulty settings.']);
        exit;
    }

    $difficulty_name = $diff_map[$diff_val];

    // Check if user has already claimed a coupon (one-time rule)
    // First, check if the table exists to avoid fatal errors if fix_db.php wasn't run
    $table_check = $conn->query("SHOW TABLES LIKE 'game_log'");
    if ($table_check->num_rows == 0) {
        throw new Exception("Database mismatch: 'game_log' table is missing. Please run fix_db.php.");
    }

    $stmt = $conn->prepare("SELECT id FROM game_log WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'You have already claimed a reward for this email.']);
        exit;
    }

    // Reward mapping
    $discount = 0;
    $max_uses = 1;

    if ($diff_val == "10") {
        $discount = 5.00;
    } elseif ($diff_val == "15") {
        $discount = 7.00;
    } elseif ($diff_val == "25") {
        $discount = 15.00;
        $max_uses = 2;
    } elseif ($diff_val == "38") {
        $discount = 20.00;
        $max_uses = 2;
    }

    // Generate unique coupon code
    $coupon_code = "MAZE-" . strtoupper(substr(md5(uniqid($email, true)), 0, 6));

    // Start transaction
    $conn->begin_transaction();

    // Insert into coupons table
    $stmt = $conn->prepare("INSERT INTO coupons (code, discount, email, max_uses, difficulty, status) VALUES (?, ?, ?, ?, ?, 'Active')");
    $stmt->bind_param("sdsis", $coupon_code, $discount, $email, $max_uses, $difficulty_name);
    $stmt->execute();

    // Log the play
    $stmt = $conn->prepare("INSERT INTO game_log (email, difficulty) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $difficulty_name);
    $stmt->execute();

    $conn->commit();
    echo json_encode(['success' => true, 'coupon_code' => $coupon_code]);

} catch (Exception $e) {
    if (isset($conn)) $conn->rollback();
    error_log("Maze Game Reward Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

if (isset($conn)) $conn->close();
?>
