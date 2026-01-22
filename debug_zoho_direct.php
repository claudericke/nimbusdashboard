<?php
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once __DIR__ . '/app/Helpers/functions.php';
require_once __DIR__ . '/app/Services/ZohoService.php';

$zoho = new ZohoService();

// Define test parameters (Replace these with the actual values causing issues if possible, or use a known test case)
// For now, I'll prompt the user or hardcode a placeholder. I will try to read from command args if possible, or just hardcode for the user to edit.
// Let's use a dummy domain that likely exists or the one the user might have tested.
// User didn't specify the domain, so I'll check the DB for a user or use a placeholder.
// I'll assume 'cloud.driftnimbus.com' or similar, but better to fetch the first user from DB.

require_once __DIR__ . '/app/Helpers/Database.php';
$db = Database::getInstance();
// Fetch the most recent user, as that's likely who the user is testing
$userResult = $db->query("SELECT domain, email FROM users ORDER BY id DESC LIMIT 1");
$user = $userResult->fetch_assoc();

$domain = $user['domain'] ?? 'example.com';
$email = $user['email'] ?? 'test@example.com';

ob_start();
echo "Debugging Zoho for Domain: $domain, Email: $email\n";
echo "------------------------------------------------\n";

// 1. Search by customer_name (Exact/Contains logic of Zoho?)
echo "1. Search by customer_name='$domain': ...\n";
$res1 = $zoho->call("invoices?customer_name=" . urlencode($domain));
echo "   Found: " . count($res1['invoices'] ?? []) . " invoices.\n";
if (empty($res1['invoices'])) {
    echo "   Raw Response: " . json_encode($res1) . "\n";
}

// 2. Search by company_name_contains
echo "\n2. Search Contacts by company_name_contains='$domain': ...\n";
$res2 = $zoho->call("contacts?company_name_contains=" . urlencode($domain));
$contactsByName = $res2['contacts'] ?? [];
echo "   Found: " . count($contactsByName) . " contacts.\n";
foreach ($contactsByName as $c) {
    echo "   - ID: {$c['contact_id']}, Name: {$c['contact_name']}, Company: {$c['company_name']}\n";
}

// 3. Search by email_contains
echo "\n3. Search Contacts by email_contains='$email': ...\n";
$res3 = $zoho->call("contacts?email_contains=" . urlencode($email));
$contactsByEmail = $res3['contacts'] ?? [];
echo "   Found: " . count($contactsByEmail) . " contacts.\n";
foreach ($contactsByEmail as $c) {
    echo "   - ID: {$c['contact_id']}, Name: {$c['contact_name']}, Email: {$c['email']}\n";
}

// 4. Test Service Method Logic
echo "\n4. Testing ZohoService::getContactId logic:\n";
$contactId = $zoho->getContactId($email, $domain);
echo "   Resolved Contact ID: " . ($contactId ?? 'NULL') . "\n";

if ($contactId) {
    echo "   Fetching invoices for Contact ID $contactId...\n";
    $invoices = $zoho->getInvoices($domain, $email);
    echo "   Service returned " . count($invoices) . " invoices.\n";
} else {
    echo "   Fallback to default search...\n";
    $invoices = $zoho->getInvoices($domain, $email);
    echo "   Service returned " . count($invoices) . " invoices.\n";
}

$output = ob_get_clean();
file_put_contents(__DIR__ . '/zoho_debug_output.txt', $output);
echo "Debug output written to zoho_debug_output.txt";
