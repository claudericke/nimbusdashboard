<?php
require_once __DIR__ . '/index.php';

$db = Database::getInstance();
$result = $db->query("SHOW TABLES LIKE 'activity_logs'");
if ($result->num_rows > 0) {
    echo "Table 'activity_logs' exists.\n";
} else {
    echo "Table 'activity_logs' DOES NOT exist.\n";
    exit;
}

$activityLog = new ActivityLog();
// Mock a user ID 1 for testing
if ($activityLog->log(1, 'test', 'Test log entry')) {
    echo "Log insertion successful.\n";
} else {
    echo "Log insertion failed.\n";
}

$logs = $activityLog->getRecent(1);
if (!empty($logs) && $logs[0]['description'] === 'Test log entry') {
    echo "Log retrieval successful.\n";
} else {
    echo "Log retrieval failed.\n";
    print_r($logs);
}
