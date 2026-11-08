<?php
// Set error reporting to log to a file instead of outputting to break JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_error.log');

header('Content-Type: application/json');

try {
    include 'includes/db.php';
    if (!$conn) {
        throw new Exception("Database connection failed.");
    }

    // Enable mysqli exceptions
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    // Get POST data
    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);

    if (!isset($data['code']) || !isset($data['email'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request data.']);
        exit;
    }

    $code = $data['code'];
    $email = $data['email'];

    // Fetch coupon details
    $stmt = $conn->prepare("SELECT * FROM coupons WHERE code = ? AND status = 'Active'");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired coupon code.']);
        exit;
    }

    $coupon = $result->fetch_assoc();

    // Check email binding (exact match - case-sensitive)
    if (!empty($coupon['email']) && $coupon['email'] !== $email) {
        echo json_encode(['success' => false, 'message' => 'This coupon is bound to another email.']);
        exit;
    }

    // Check usage limits
    // Note: Column names must match those in fix_db.php
    if (isset($coupon['used_count']) && isset($coupon['max_uses'])) {
        if ($coupon['used_count'] >= $coupon['max_uses']) {
            echo json_encode(['success' => false, 'message' => 'This coupon has already reached its maximum usage limit.']);
            exit;
        }
    }

    // Successful validation
    echo json_encode([
        'success' => true,
        'discount' => $coupon['discount'],
        'code' => $coupon['code']
    ]);

} catch (Exception $e) {
    error_log("Coupon Validation Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Validation error: ' . $e->getMessage()]);
}

if (isset($conn)) $conn->close();
?>
