<?php
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/app/Helpers/functions.php';

// Mock Session for testing
class Session
{
    public static function getUsername()
    {
        return env('WHM_USER');
    }
    public static function getApiToken()
    {
        return env('WHM_API_KEY');
    }
    public static function getDomain()
    {
        return 'driftnimbus.com';
    } // Test domain
}

echo "--- Testing Cpanel Service ---\n";
try {
    $cpanel = new CpanelService();

    echo "Checking Server Status...\n";
    $status = $cpanel->checkServerStatus();
    echo "Status: " . ($status ? "Online" : "Offline") . "\n";

    echo "\nFetching Disk Usage...\n";
    $disk = $cpanel->getDiskUsage();
    echo "Disk Usage Data: " . print_r($disk['data'], true) . "\n";

    echo "\nFetching SSL Certs...\n";
    $ssl = $cpanel->getSslCerts();
    echo "SSL Certs Count: " . count($ssl['data'] ?? []) . "\n";
    if (!empty($ssl['data'])) {
        foreach ($ssl['data'] as $cert) {
            echo " - Cert: " . ($cert['subject']['commonName'] ?? 'Unknown') . " (Expires: " . ($cert['notafter'] ?? 'N/A') . ")\n";
        }
    }

} catch (Exception $e) {
    echo "Cpanel Error: " . $e->getMessage() . "\n";
}
