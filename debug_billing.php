<?php
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once __DIR__ . '/app/Helpers/functions.php';
require_once __DIR__ . '/app/Services/ZohoService.php';
require_once __DIR__ . '/app/Services/PaynowService.php';
require_once __DIR__ . '/app/Helpers/Database.php';

$db = Database::getInstance();
$userResult = $db->query("SELECT domain, email FROM users ORDER BY id DESC LIMIT 1");
$user = $userResult->fetch_assoc();

$domain = $user['domain'] ?? 'driftstudio.co.zw';
$email = $user['email'] ?? 'hello@driftstudio.co.zw';

ob_start();
echo "Debugging for: $domain ($email)\n";

$zohoService = new ZohoService();
$paynowService = new PaynowService();

$invoices = $zohoService->getInvoices($domain, $email);

echo "Found " . count($invoices) . " invoices.\n";

foreach ($invoices as $invoice) {
    echo "--------------------------------------------------\n";
    echo "Invoice: " . ($invoice['invoice_number'] ?? 'N/A') . "\n";
    echo "Status: '" . ($invoice['status'] ?? 'N/A') . "'\n";
    echo "Balance: '" . ($invoice['balance'] ?? 'N/A') . "' (Type: " . gettype($invoice['balance']) . ")\n";

    // Simulate Controller Logic
    $isUnpaid = ($invoice['status'] === 'unpaid');
    $hasBalance = ($invoice['balance'] > 0);

    echo "Logic Check:\n";
    echo "  - Status === 'unpaid': " . ($isUnpaid ? 'TRUE' : 'FALSE') . "\n";
    echo "  - Balance > 0: " . ($hasBalance ? 'TRUE' : 'FALSE') . "\n";

    if ($isUnpaid && $hasBalance) {
        $url = $paynowService->generatePaymentUrl(
            $invoice['invoice_id'],
            $invoice['balance'],
            $invoice['invoice_number'],
            $email
        );
        echo "  -> WOULD GENERATE URL: $url\n";
    } else {
        echo "  -> NO URL GENERATED (Conditions failed)\n";
    }
}

file_put_contents(__DIR__ . '/billing_debug.txt', ob_get_clean());
echo "Debug output written to billing_debug.txt";
