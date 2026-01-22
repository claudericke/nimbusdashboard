<?php
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "DB_HOST in _ENV: " . (isset($_ENV['DB_HOST']) ? 'Yes' : 'No') . "\n";
echo "DB_HOST in getenv: " . (getenv('DB_HOST') ? 'Yes' : 'No') . "\n";
echo "DB_HOST in _SERVER: " . (isset($_SERVER['DB_HOST']) ? 'Yes' : 'No') . "\n";
