<?php
// Load environment variables
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Load autoloader (custom one if exists, or just rely on composer if everything is mapped there)
// Checking index.php, it loads a custom 'autoload.php'
require_once __DIR__ . '/autoload.php';

// Load helper functions
require_once __DIR__ . '/app/Helpers/functions.php';

// We don't need Session or Router for this model test

$perm = new Permission();
$role = 'test_role_cli_' . time();
$menu = 'test_menu_cli';

echo "Attempting to UPSERT permission for non-existent role: $role\n";

// 1. Test Insert
$success = $perm->update($role, $menu, 1);
if ($success) {
    echo "First Update (Insert) returned true.\n";
} else {
    echo "First Update (Insert) returned false.\n";
}

// 2. Verify Insert
$access = $perm->canAccess($menu, $role);
echo "Can Access after Insert: " . ($access ? 'Yes' : 'No') . "\n";

// 3. Test Update (Duplicate Key)
$success = $perm->update($role, $menu, 0);
if ($success) {
    echo "Second Update (Update) returned true.\n";
} else {
    echo "Second Update (Update) returned false.\n";
}

// 4. Verify Update
$access = $perm->canAccess($menu, $role);
echo "Can Access after Update: " . ($access ? 'Yes' : 'No') . "\n";

// Clean up
$db = Database::getInstance();
$db->query("DELETE FROM permissions WHERE role_name = '$role'");
echo "Cleaned up.\n";
