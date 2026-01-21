<?php
$db = new mysqli('127.0.0.1', 'root', '', 'driftnimbus_dashboard');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$sql = "ALTER TABLE users ADD COLUMN email VARCHAR(255) AFTER full_name";
if ($db->query($sql)) {
    echo "Column 'email' added successfully.\n";
} else {
    echo "Error adding column: " . $db->error . "\n";
}

$db->close();
