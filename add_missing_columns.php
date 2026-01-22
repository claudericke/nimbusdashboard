<?php
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once __DIR__ . '/app/Helpers/functions.php';
require_once __DIR__ . '/app/Helpers/Database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

echo "Checking and adding missing columns...\n";

// Add email
$check = $conn->query("SHOW COLUMNS FROM users LIKE 'email'");
if ($check->num_rows == 0) {
    if ($conn->query("ALTER TABLE users ADD COLUMN email VARCHAR(255) AFTER domain")) {
        echo "Added 'email' column.\n";
    } else {
        echo "Error adding 'email': " . $conn->error . "\n";
    }
} else {
    echo "'email' column already exists.\n";
}

// Add user_role
$check = $conn->query("SHOW COLUMNS FROM users LIKE 'user_role'");
if ($check->num_rows == 0) {
    if ($conn->query("ALTER TABLE users ADD COLUMN user_role VARCHAR(50) DEFAULT 'client' AFTER is_superuser")) {
        echo "Added 'user_role' column.\n";
    } else {
        echo "Error adding 'user_role': " . $conn->error . "\n";
    }
} else {
    echo "'user_role' column already exists.\n";
}

echo "Done.";
