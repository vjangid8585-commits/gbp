<?php
echo "Starting DB Setup...\n";
if (!class_exists('mysqli')) {
    die("Error: mysqli class not found in script!\n");
}
echo "mysqli class exists.\n";

$servername = "127.0.0.1";
$username = "root";
$password = "";

// Create connection
echo "Connecting to mysql...\n";
try {
    $conn = new mysqli($servername, $username, $password);
} catch (Exception $e) {
    die("Exception connecting: " . $e->getMessage() . "\n");
}

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error . "\n");
}
echo "Connected successfully.\n";

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS gbp_db";
if ($conn->query($sql) === TRUE) {
  echo "Database created successfully\n";
} else {
  die("Error creating database: " . $conn->error . "\n");
}

$conn->select_db("gbp_db");

// Users Table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager', 'staff') NOT NULL DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL
)";
if (!$conn->query($sql)) echo "Error creating users: " . $conn->error . "\n";

// Settings Table
$sql = "CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT
)";
if (!$conn->query($sql)) echo "Error creating settings: " . $conn->error . "\n";

// Locations Table
$sql = "CREATE TABLE IF NOT EXISTS locations (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    google_location_id VARCHAR(255) NOT NULL UNIQUE,
    account_id VARCHAR(255) NOT NULL,
    business_name VARCHAR(255) NOT NULL,
    address_json TEXT,
    internal_assignee_id INT(11) UNSIGNED,
    data_json LONGTEXT,
    sync_status VARCHAR(20) DEFAULT 'pending', 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL
)";
if (!$conn->query($sql)) echo "Error creating locations: " . $conn->error . "\n";

// Insights Table
$sql = "CREATE TABLE IF NOT EXISTS insights (
    id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    location_id INT(11) UNSIGNED NOT NULL,
    date DATE NOT NULL,
    calls INT DEFAULT 0,
    website_clicks INT DEFAULT 0,
    direction_requests INT DEFAULT 0,
    chat_messages INT DEFAULT 0,
    search_views INT DEFAULT 0,
    maps_views INT DEFAULT 0,
    total_interactions INT DEFAULT 0,
    UNIQUE KEY loc_date (location_id, date)
)";
if (!$conn->query($sql)) echo "Error creating insights: " . $conn->error . "\n";

// Reviews Table
$sql = "CREATE TABLE IF NOT EXISTS reviews (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    location_id INT(11) UNSIGNED NOT NULL,
    google_review_id VARCHAR(255) NOT NULL UNIQUE,
    reviewer_name VARCHAR(255),
    rating INT(1),
    comment TEXT,
    reply_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
if (!$conn->query($sql)) echo "Error creating reviews: " . $conn->error . "\n";

// Posts Table
$sql = "CREATE TABLE IF NOT EXISTS posts (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    location_id INT(11) UNSIGNED NOT NULL,
    google_post_id VARCHAR(255) UNIQUE,
    content TEXT,
    media_url TEXT,
    topic_type VARCHAR(50),
    status VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (!$conn->query($sql)) echo "Error creating posts: " . $conn->error . "\n";

echo "Tables created successfully\n";
$conn->close();
?>
