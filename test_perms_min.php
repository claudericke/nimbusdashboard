<?php
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
require_once __DIR__ . '/autoload.php';

// require_once __DIR__ . '/app/Helpers/functions.php'; // Commented out

$perm = new Permission();
$role = 'test_role_min_' . time();
$menu = 'test_menu_min';

echo "Testing UPSERT logic...\n";
if ($perm->update($role, $menu, 1)) {
    echo "Update success\n";
} else {
    echo "Update failed\n";
}

$db = Database::getInstance();
$db->query("DELETE FROM permissions WHERE role_name = '$role'");
echo "Done.\n";
