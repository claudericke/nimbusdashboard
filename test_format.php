<?php
require_once __DIR__ . '/app/Helpers/functions.php';

$testName = 'Nimbus Dashboard Onboarding- {"id":"domainName","type":"text","title":"Domain","value":"trababalas.com","raw_value":"trababalas.com","required":"1"}';
echo "Original: " . $testName . "\n";
echo "Formatted: " . formatTicketName($testName) . "\n";
