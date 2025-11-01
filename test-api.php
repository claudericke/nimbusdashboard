<?php
session_start();
require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? '');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? '');
define('VERIFY_SSL', false);
define('CURL_TIMEOUT', 30);

function h($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function curl_ssl_opts() {
    return [
        CURLOPT_SSL_VERIFYPEER => VERIFY_SSL ? true : false,
        CURLOPT_SSL_VERIFYHOST => VERIFY_SSL ? 2 : 0,
    ];
}

function uapi_call($domain, $user, $token, $module, $function, $args = []) {
    $qs = $args ? ('?' . http_build_query($args)) : '';
    $url = "https://{$domain}:2083/execute/{$module}/{$function}{$qs}";
    $ch = curl_init($url);
    curl_setopt_array($ch, array_replace([
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => CURL_TIMEOUT,
        CURLOPT_HTTPHEADER     => ['Authorization: cpanel ' . $user . ':' . $token],
    ], curl_ssl_opts()));

    $resp = curl_exec($ch);
    if ($resp === false) {
        $err = curl_error($ch);
        curl_close($ch);
        throw new Exception('cURL error: ' . $err);
    }
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $json = json_decode($resp, true);
    if ($code !== 200 || !isset($json['status']) || intval($json['status']) !== 1) {
        $error_message = $json['errors'][0] ?? 'Unknown cPanel error';
        throw new Exception("cPanel API Error: " . $error_message);
    }
    return $json;
}

$testFunctions = [
    ['Email', 'list_pops'],
    ['Email', 'list_pops_with_disk'],
    ['DomainInfo', 'list_domains'],
    ['SSL', 'list_certs'],
    ['Quota', 'get_quota_info'],
    ['Mysql', 'list_databases'],
    ['Ftp', 'list_ftp'],
    ['StatsBar', 'get_stats'],
    ['Bandwidth', 'get_retention_periods'],
    ['Fileman', 'get_file_information'],
];

$results = [];

if (isset($_SESSION['cpanel_username'], $_SESSION['cpanel_domain'], $_SESSION['cpanel_api_token'])) {
    $cpanelUser = $_SESSION['cpanel_username'];
    $cpanelDomain = $_SESSION['cpanel_domain'];
    $cpanelApiToken = $_SESSION['cpanel_api_token'];

    foreach ($testFunctions as $func) {
        $module = $func[0];
        $function = $func[1];
        try {
            $response = uapi_call($cpanelDomain, $cpanelUser, $cpanelApiToken, $module, $function);
            $results[] = [
                'module' => $module,
                'function' => $function,
                'status' => 'SUCCESS',
                'data' => $response['data'] ?? null,
            ];
        } catch (Exception $e) {
            $results[] = [
                'module' => $module,
                'function' => $function,
                'status' => 'ERROR',
                'error' => $e->getMessage(),
            ];
        }
    }
} else {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>cPanel API Test</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { background: #1e1e1e; color: #fff; padding: 20px; }
        .result-card { background: #2a2a2a; border: 1px solid #3a3a3a; margin-bottom: 20px; }
        .success { border-left: 4px solid #28a745; }
        .error { border-left: 4px solid #dc3545; }
        pre { background: #1a1a1a; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">cPanel API Function Test</h1>
        <p class="mb-4">Testing domain: <strong><?php echo h($cpanelDomain); ?></strong></p>
        <a href="index.php" class="btn btn-primary mb-4">Back to Dashboard</a>

        <?php foreach ($results as $result): ?>
            <div class="card result-card <?php echo $result['status'] === 'SUCCESS' ? 'success' : 'error'; ?>">
                <div class="card-body">
                    <h5 class="card-title">
                        <?php echo h($result['module']); ?>::<?php echo h($result['function']); ?>
                        <span class="badge bg-<?php echo $result['status'] === 'SUCCESS' ? 'success' : 'danger'; ?> float-end">
                            <?php echo h($result['status']); ?>
                        </span>
                    </h5>
                    <?php if ($result['status'] === 'SUCCESS'): ?>
                        <p class="text-success">✓ Function returned data successfully</p>
                        <details>
                            <summary style="cursor: pointer;">View Response Data</summary>
                            <pre><?php echo h(json_encode($result['data'], JSON_PRETTY_PRINT)); ?></pre>
                        </details>
                    <?php else: ?>
                        <p class="text-danger">✗ Error: <?php echo h($result['error']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
