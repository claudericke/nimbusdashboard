<?php
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once __DIR__ . '/app/Helpers/functions.php';
require_once __DIR__ . '/app/Helpers/Database.php';

$db = Database::getInstance();
$result = $db->query("SHOW COLUMNS FROM users");

if ($result) {
    $output = "Columns in 'users' table:\n";
    while ($row = $result->fetch_assoc()) {
        $output .= "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    file_put_contents(__DIR__ . '/schema_output.txt', $output);
    echo "Output written to schema_output.txt";
} else {
    echo "Error showing columns: " . $db->getConnection()->error;
}