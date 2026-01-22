<?php
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once __DIR__ . '/app/Helpers/functions.php';
require_once __DIR__ . '/app/Helpers/Database.php';
require_once __DIR__ . '/app/Services/MigrationService.php';

$service = new MigrationService();
$migrations = $service->getMigrations();

echo "Migrations found: " . count($migrations) . "\n";
foreach ($migrations as $m) {
    echo "- " . $m['filename'] . " (" . $m['status'] . ")\n";
}
