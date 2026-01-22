<?php
require_once __DIR__ . '/index.php';

$db = Database::getInstance();
$result = $db->query("SELECT * FROM permissions ORDER BY role_name, menu_item");
$perms = $result->fetch_all(MYSQLI_ASSOC);

echo "Total Permissions Logs: " . count($perms) . "\n";
foreach ($perms as $p) {
    echo "{$p['role_name']} | {$p['menu_item']} | {$p['can_access']}\n";
}
