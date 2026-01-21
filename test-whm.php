<?php
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/app/Helpers/functions.php';

$host = env('WHM_HOST'); // amsr200.websitehostserver.net
$user = env('WHM_USER');
$token = env('WHM_API_KEY');
$port = 2087;

$endpoint = "accountsummary?api.version=1&user=driftnim";
$url = "https://{$host}:{$port}/json-api/{$endpoint}";

echo "Testing WHM API: {$url}\n";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: whm {$user}:{$token}"]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: {$httpCode}\n";
echo "Response: " . $response . "\n";
