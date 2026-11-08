<?php
// contact_form_handler.php
include 'includes/db.php';

// Response array
$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Prepare SQL query to insert data into the contact_messages table
    $sql = "INSERT INTO contact_messages (name, email, phone, subject, message) 
            VALUES (?, ?, ?, ?, ?)";

    // Prepare statement
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);

        if ($stmt->execute()) {
            $response['success'] = true;
        } else {
            $response['success'] = false;
        }

        $stmt->close();
    } else {
        $response['success'] = false;
    }

    // Close the database connection
    $conn->close();
} else {
    $response['success'] = false;
}

// Return the response as JSON
echo json_encode($response);
?>