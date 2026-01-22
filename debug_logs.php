<?php
require_once __DIR__ . '/index.php';

$db = Database::getInstance();
$result = $db->query("SELECT * FROM activity_logs ORDER BY id DESC LIMIT 20");
$logs = $result->fetch_all(MYSQLI_ASSOC);

echo "Total Logs: " . count($logs) . "\n";
foreach ($logs as $log) {
    echo "[ID: {$log['id']}] User: {$log['user_id']} | Action: {$log['action_type']} | Desc: {$log['description']} | Time: {$log['created_at']}\n";
}

// Clean up test data
$db->query("DELETE FROM activity_logs WHERE description = 'Test log entry'");
echo "\nDeleted test logs.\n";
