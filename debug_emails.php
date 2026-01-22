<?php
require_once __DIR__ . '/index.php';

$cpanelService = new CpanelService();
$emails = $cpanelService->getEmails(1, 20);

echo "Total Emails Count: " . count($emails['data'] ?? []) . "\n";
print_r($emails);
