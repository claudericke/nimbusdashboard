<?php
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/app/Helpers/functions.php';
require_once __DIR__ . '/helpers.php';

// Initialize Zoho
echo "--- Testing Zoho Service ---\n";
try {
    $zoho = new ZohoService();
    $invoices = $zoho->getInvoices('driftnimbus.com'); // Test with default domain
    echo "Successfully fetched " . count($invoices) . " invoices.\n";
    if (!empty($invoices)) {
        echo "Example Invoice ID: " . $invoices[0]['invoice_id'] . "\n";
    }
} catch (Exception $e) {
    echo "Zoho Error: " . $e->getMessage() . "\n";
}

echo "\n--- Testing Trello Service (Open Tickets) ---\n";
try {
    $trello = new TrelloService();
    $tickets = $trello->getOpenTickets();
    echo "Successfully fetched " . count($tickets) . " open tickets.\n";
    foreach ($tickets as $ticket) {
        echo " - [{$ticket['listName']}] {$ticket['name']} (ID: {$ticket['id']})\n";
    }
} catch (Exception $e) {
    echo "Trello Error: " . $e->getMessage() . "\n";
}
