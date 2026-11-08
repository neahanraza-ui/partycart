<?php
include 'admin/db.php';

// Create table
$sql = "CREATE TABLE IF NOT EXISTS meta_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_name VARCHAR(50) NOT NULL UNIQUE,
    meta_title VARCHAR(255),
    meta_description TEXT,
    meta_keywords TEXT
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'meta_tags' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Default pages to insert
$pages = [
    'index' => ['Home', 'Welcome to Luxora', 'luxury, fashion, home'],
    'about' => ['About Us', 'Learn more about Luxora', 'about, story, team'],
    'collections' => ['Collections', 'Explore our collections', 'collections, seasonal, trends'],
    'contact' => ['Contact Us', 'Get in touch with us', 'contact, support, help'],
    'products' => ['Products', 'Browse our products', 'shopping, store, items'],
    'login' => ['Login', 'Login to your account', 'login, account, member'],
    'signup' => ['Sign Up', 'Create a new account', 'register, join, sign up']
];

foreach ($pages as $page => $defaults) {
    $stmt = $conn->prepare("INSERT IGNORE INTO meta_tags (page_name, meta_title, meta_description, meta_keywords) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $page, $defaults[0], $defaults[1], $defaults[2]);
    if ($stmt->execute()) {
        echo "Inserted/Ignored default for page: $page<br>";
    } else {
        echo "Error inserting $page: " . $stmt->error . "<br>";
    }
}

echo "Setup complete. Please delete this file.";
?>
