<?php
echo "Adding Products and Services tables...\n";

$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "gbp_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

// Products Table
$sql = "CREATE TABLE IF NOT EXISTS products (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    location_id INT(11) UNSIGNED NOT NULL,
    google_product_id VARCHAR(255) UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2),
    category VARCHAR(100),
    image_url TEXT,
    product_data_json LONGTEXT,
    sync_status ENUM('pending', 'synced', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_location (location_id),
    INDEX idx_sync_status (sync_status)
)";
if ($conn->query($sql) === TRUE) {
    echo "Products table created successfully\n";
} else {
    echo "Error creating products: " . $conn->error . "\n";
}

// Services Table
$sql = "CREATE TABLE IF NOT EXISTS services (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    location_id INT(11) UNSIGNED NOT NULL,
    google_service_id VARCHAR(255),
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2),
    price_type ENUM('fixed', 'hourly', 'free', 'varies') DEFAULT 'fixed',
    category VARCHAR(100),
    display_order INT DEFAULT 0,
    service_data_json LONGTEXT,
    sync_status ENUM('pending', 'synced', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_location (location_id),
    INDEX idx_display_order (display_order),
    INDEX idx_sync_status (sync_status)
)";
if ($conn->query($sql) === TRUE) {
    echo "Services table created successfully\n";
} else {
    echo "Error creating services: " . $conn->error . "\n";
}

// Add scheduled_at and post_data_json columns to posts table if not exists
$columns_to_add = [
    "ALTER TABLE posts ADD COLUMN scheduled_at DATETIME NULL AFTER status",
    "ALTER TABLE posts ADD COLUMN post_data_json LONGTEXT NULL AFTER media_url"
];

foreach ($columns_to_add as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Column added successfully\n";
    } else {
        if (strpos($conn->error, 'Duplicate column') !== false) {
            echo "Column already exists (OK)\n";
        } else {
            echo "Note: " . $conn->error . "\n";
        }
    }
}

echo "\nSetup complete!\n";
$conn->close();
?>
