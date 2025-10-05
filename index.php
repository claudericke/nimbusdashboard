<?php
session_start();

// -------------------- BOOTSTRAP: Composer + .env --------------------
require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// -------------------- CONFIG --------------------
// MySQL
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? '');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? '');

// Security toggles
define('VERIFY_SSL', false); // TODO: set to true in production once CA chain is valid
define('CURL_TIMEOUT', 30);

// External APIs
define('WEATHER_API_URL', 'https://api.open-meteo.com/v1/forecast?latitude=-17.8248&longitude=31.0530&current=temperature_2m,weather_code&timezone=auto');
define('NEWS_RSS_URL', 'https://feeds.bbci.co.uk/news/world/rss.xml');

// Zoho Books API
define('ZOHO_ORGANIZATION_ID', $_ENV['ZOHO_ORGANIZATION_ID'] ?? 'YOUR_ZOHO_ORG_ID_HERE');
define('ZOHO_CLIENT_ID', $_ENV['ZOHO_CLIENT_ID'] ?? '');
define('ZOHO_CLIENT_SECRET', $_ENV['ZOHO_CLIENT_SECRET'] ?? '');
define('ZOHO_REFRESH_TOKEN', $_ENV['ZOHO_REFRESH_TOKEN'] ?? '');



// -------------------- HELPERS --------------------
require 'helpers.php';

function h($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function json_try_decode($str)
{
    $j = json_decode($str, true);
    return is_array($j) ? $j : [];
}

// -------------------- DB --------------------
function getDbConnection()
{
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        // Keep die() to make failures obvious in this admin tool
        die("Database connection failed: " . h($conn->connect_error));
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}

function getUserProfile($username, $domain)
{
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT * FROM users WHERE cpanel_username = ? AND domain = ? LIMIT 1");
    $stmt->bind_param("ss", $username, $domain);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc() ?: null;
    $stmt->close();
    $conn->close();
    return $user;
}

function updateUserProfile($username, $domain, $fullName, $profilePictureUrl, $apiToken)
{
    $conn = getDbConnection();
    $stmt = $conn->prepare("
        INSERT INTO users (cpanel_username, domain, api_token, full_name, profile_picture_url)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE api_token = VALUES(api_token), full_name = VALUES(full_name), profile_picture_url = VALUES(profile_picture_url)
    ");
    $stmt->bind_param("sssss", $username, $domain, $apiToken, $fullName, $profilePictureUrl);
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}

function getQuote()
{
    $conn = getDbConnection();
    $result = $conn->query("SELECT quote_text, author, image_url FROM quotes ORDER BY RAND() LIMIT 1");
    $quote = $result ? $result->fetch_assoc() : null;
    $conn->close();
    return $quote;
}

function getQuotes()
{
    $conn = getDbConnection();
    $result = $conn->query("SELECT * FROM quotes ORDER BY id DESC");
    $quotes = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $conn->close();
    return $quotes;
}

function addQuote($quote_text, $author, $image_url)
{
    $conn = getDbConnection();
    $stmt = $conn->prepare("INSERT INTO quotes (quote_text, author, image_url) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $quote_text, $author, $image_url);
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}

function updateQuote($id, $quote_text, $author, $image_url)
{
    $conn = getDbConnection();
    $stmt = $conn->prepare("UPDATE quotes SET quote_text = ?, author = ?, image_url = ? WHERE id = ?");
    $stmt->bind_param("sssi", $quote_text, $author, $image_url, $id);
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}

function deleteQuote($id)
{
    $conn = getDbConnection();
    $stmt = $conn->prepare("DELETE FROM quotes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}

// -------------------- CPANEL UAPI --------------------
function curl_ssl_opts()
{
    return [
        CURLOPT_SSL_VERIFYPEER => VERIFY_SSL ? true : false,
        CURLOPT_SSL_VERIFYHOST => VERIFY_SSL ? 2 : 0,
    ];
}

/**
 * Call cPanel UAPI with a token
 * @throws Exception on error
 */
function uapi_call($domain, $user, $token, $module, $function, $args = [])
{
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

    $json = json_try_decode($resp);
    if ($code !== 200 || !isset($json['status']) || intval($json['status']) !== 1) {
        $error_message = $json['errors'][0] ?? 'Unknown cPanel error';
        throw new Exception("cPanel API Error: " . $error_message);
    }
    return $json;
}

/**
 * Create a new API Token using Basic Auth (username/password)
 * @return string api token
 * @throws Exception on error
 */
function cpanel_create_token_with_password($domain, $username, $password, $label = 'DriftNimbusDashboard')
{
    $url = "https://{$domain}:2083/execute/ApiTokens/create_token";
    $query = http_build_query(['label' => $label]);
    $ch = curl_init("{$url}?{$query}");
    curl_setopt_array($ch, array_replace([
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD        => $username . ':' . $password, // Basic auth
        CURLOPT_TIMEOUT        => CURL_TIMEOUT,
    ], curl_ssl_opts()));
    $response = curl_exec($ch);
    if ($response === false) {
        $err = curl_error($ch);
        curl_close($ch);
        throw new Exception('cURL error: ' . $err);
    }
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $api_token_data = json_try_decode($response);
    // Expected: ["status" => 1, "data" => [{"token" => "...", "label" => "..."}], ...]
    if ($code === 200 && isset($api_token_data['status']) && intval($api_token_data['status']) === 1) {
        $token = $api_token_data['data'][0]['token'] ?? null;
        if ($token) return $token;
    }
    $err = $api_token_data['errors'][0] ?? 'Token creation failed';
    throw new Exception($err);
}


// -------------------- NEWS API --------------------
function getNewsArticles($page = 1, $perPage = 5, $category = 'local')
{
    $categoryFeeds = [
        'local' => 'https://news.google.com/rss/search?q=Zimbabwe&hl=en-ZW&gl=ZW&ceid=ZW:en',
        'world' => 'https://feeds.bbci.co.uk/news/world/rss.xml',
        'business' => 'https://feeds.bbci.co.uk/news/business/rss.xml',
        'technology' => 'https://feeds.bbci.co.uk/news/technology/rss.xml',
        'entertainment' => 'https://feeds.bbci.co.uk/news/entertainment_and_arts/rss.xml',
        'sports' => 'https://feeds.bbci.co.uk/sport/rss.xml'
    ];

    $feedUrl = $categoryFeeds[$category] ?? $categoryFeeds['local'];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $feedUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ]);
    $news_response = curl_exec($ch);
    curl_close($ch);

    if ($news_response) {
        $news = @simplexml_load_string($news_response);
        if ($news && isset($news->channel->item)) {
            $allItems = [];
            foreach ($news->channel->item as $item) {
                $allItems[] = $item;
            }
            $allItems = array_slice($allItems, 0, 20);

            usort($allItems, function ($a, $b) {
                return strtotime($b->pubDate) - strtotime($a->pubDate);
            });

            $totalPages = max(1, ceil(count($allItems) / $perPage));
            $offset = ($page - 1) * $perPage;
            $articles = array_slice($allItems, $offset, $perPage);

            return [
                'articles' => $articles,
                'totalPages' => $totalPages,
                'currentPage' => $page
            ];
        }
    }

    return ['articles' => [], 'totalPages' => 1, 'currentPage' => 1];
}

// -------------------- ZOHO API --------------------
function zoho_refresh_token()
{
    $url = 'https://accounts.zoho.com/oauth/v2/token';
    $params = [
        'refresh_token' => ZOHO_REFRESH_TOKEN,
        'client_id' => ZOHO_CLIENT_ID,
        'client_secret' => ZOHO_CLIENT_SECRET,
        'grant_type' => 'refresh_token'
    ];
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($params)
    ]);
    $resp = curl_exec($ch);
    curl_close($ch);
    $json = json_try_decode($resp);
    if (isset($json['access_token'])) {
        $envFile = __DIR__ . '/.env';
        $envContent = file_get_contents($envFile);
        $envContent = preg_replace('/ZOHO_ACCESS_TOKEN=.*/', 'ZOHO_ACCESS_TOKEN=' . $json['access_token'], $envContent);
        file_put_contents($envFile, $envContent);
        return $json['access_token'];
    }
    throw new Exception('Failed to refresh Zoho token');
}

function zoho_call($endpoint, $args = [])
{
    $accessToken = $_ENV['ZOHO_ACCESS_TOKEN'] ?? 'YOUR_ZOHO_ACCESS_TOKEN_HERE';
    if ($accessToken === 'YOUR_ZOHO_ACCESS_TOKEN_HERE') {
        throw new Exception("Zoho API not configured.");
    }
    $url = "https://www.zohoapis.com/books/v3/{$endpoint}?organization_id=" . ZOHO_ORGANIZATION_ID;
    if (!empty($args)) {
        $url .= '&' . http_build_query($args);
    }
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Zoho-oauthtoken ' . $accessToken
        ],
    ]);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $json = json_try_decode($resp);
    if ($code === 401) {
        $accessToken = zoho_refresh_token();
        $_ENV['ZOHO_ACCESS_TOKEN'] = $accessToken;
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Zoho-oauthtoken ' . $accessToken
            ],
        ]);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $json = json_try_decode($resp);
    }
    if ($code !== 200 || ($json['code'] ?? null) !== 0) {
        $error_message = $json['message'] ?? ($resp ?: 'Unknown Zoho API error');
        throw new Exception("Zoho API Error (HTTP {$code}): " . $error_message);
    }
    return $json;
}

// -------------------- ROUTING STATE --------------------
$page = $_GET['page'] ?? 'dashboard';
$subPage = $_GET['sub'] ?? '';
$error = '';
$success = '';
$cPanelData = [];
$userProfile = null;
$loggedIn = false;
$isSuperuser = false;

if ($page === 'payment-success' || $page === 'payment-failed') {
    $loggedIn = true;
}

// Admin domain switching
if (isset($_GET['switch_domain']) && isset($_SESSION['is_superuser']) && $_SESSION['is_superuser'] === 1) {
    $switchDomainId = (int)$_GET['switch_domain'];
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $switchDomainId);
    $stmt->execute();
    $result = $stmt->get_result();
    $switchUser = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    if ($switchUser) {
        $_SESSION['admin_original_user'] = $_SESSION['cpanel_username'];
        $_SESSION['admin_original_domain'] = $_SESSION['cpanel_domain'];
        $_SESSION['admin_original_token'] = $_SESSION['cpanel_api_token'];
        $_SESSION['cpanel_username'] = $switchUser['cpanel_username'];
        $_SESSION['cpanel_domain'] = $switchUser['domain'];
        $_SESSION['cpanel_api_token'] = $switchUser['api_token'];
        header('Location: index.php');
        exit;
    }
}

// -------------------- AUTH HANDLERS --------------------
// Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Login POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'login') {
    csrf_check();
    $domain = trim($_POST['domain'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($domain === '' || $username === '' || $password === '') {
        $error = "Please provide domain, username and password.";
    } else {
        // 1) Try DB token first
        $userFromDb = getUserProfile($username, $domain);
        if ($userFromDb && !empty($userFromDb['api_token'])) {
            try {
                uapi_call($domain, $username, $userFromDb['api_token'], 'Email', 'list_pops');
                $_SESSION['cpanel_username']  = $username;
                $_SESSION['cpanel_domain']    = $domain;
                $_SESSION['cpanel_api_token'] = $userFromDb['api_token'];
                $_SESSION['is_superuser']     = (int)($userFromDb['is_superuser'] ?? 0);
                header('Location: index.php');
                exit;
            } catch (Exception $e) {
                // fall through to create a new token
            }
        }

        // 2) Create fresh token with password
        try {
            $apiToken = cpanel_create_token_with_password($domain, $username, $password, 'DriftNimbusDashboard');
            $_SESSION['cpanel_username']  = $username;
            $_SESSION['cpanel_domain']    = $domain;
            $_SESSION['cpanel_api_token'] = $apiToken;
            $_SESSION['is_superuser']     = (int)($userFromDb['is_superuser'] ?? 0);

            // Persist for next time
            updateUserProfile($username, $domain, $userFromDb['full_name'] ?? null, $userFromDb['profile_picture_url'] ?? null, $apiToken);

            header('Location: index.php');
            exit;
        } catch (Exception $e) {
            $error = "Login failed. " . h($e->getMessage());
        }
    }
}

// -------------------- AUTH SESSION --------------------
if (isset($_SESSION['cpanel_username'], $_SESSION['cpanel_domain'], $_SESSION['cpanel_api_token'])) {
    $loggedIn     = true;
    $cpanelUser   = $_SESSION['cpanel_username'];
    $cpanelDomain = $_SESSION['cpanel_domain'];
    $cpanelApiToken = $_SESSION['cpanel_api_token'];
    $isSuperuser  = (int)($_SESSION['is_superuser'] ?? 0);

    // Fetch user profile (optional)
    $userProfile = getUserProfile($cpanelUser, $cpanelDomain);

    try {
        if ($page === 'dashboard') {
            // Email count
            try {
                $emailList = uapi_call($cpanelDomain, $cpanelUser, $cpanelApiToken, 'Email', 'list_pops');
                $cPanelData['emailCount'] = isset($emailList['data']) && is_array($emailList['data']) ? count($emailList['data']) : 0;
            } catch (Exception $e) {
                $cPanelData['emailCount'] = 'N/A';
                $error .= "Could not retrieve email count: " . h($e->getMessage()) . " ";
            }
            $cPanelData['welcomeName'] = $userProfile['full_name'] ?? $cpanelUser;

            // Disk Usage
            try {
                $quota = uapi_call($cpanelDomain, $cpanelUser, $cpanelApiToken, 'Quota', 'get_quota_info');
                $diskUsed = $quota['data']['megabytes_used'] ?? null;
                $cPanelData['diskUsage'] = $diskUsed ? round($diskUsed / 1024, 2) : null;
            } catch (Exception $e) {
                $cPanelData['diskUsage'] = null;
            }

            // Weather, Quote, News
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 5
            ]);

            // Weather
            curl_setopt($ch, CURLOPT_URL, WEATHER_API_URL);
            $weather = json_try_decode(curl_exec($ch));
            $cPanelData['weather'] = $weather['current'] ?? [];

            // Quote from DB
            $cPanelData['quote'] = getQuote();

            // News with pagination and category
            $newsPage = max(1, (int)($_GET['news_page'] ?? 1));
            $newsCategory = $_GET['news_cat'] ?? 'world';
            $cPanelData['news'] = getNewsArticles($newsPage, 5, $newsCategory);
            $cPanelData['newsCategory'] = $newsCategory;

            curl_close($ch);
        } elseif ($page === 'domains') {
            $domains = uapi_call($cpanelDomain, $cpanelUser, $cpanelApiToken, 'DomainInfo', 'list_domains');
            $subs = $domains['data']['sub_domains'] ?? [];
            $main = $domains['data']['main_domain'] ?? '';
            $all = $subs;
            if ($main) $all[] = $main;
            $cPanelData['domains'] = array_values(array_unique(array_filter($all)));
        } elseif ($page === 'emails') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['delete_email'])) {
                    csrf_check();
                    $emailToDelete = trim($_POST['email_address'] ?? '');
                    $emailToDelete = str_replace(["\r", "\n"], '', $emailToDelete);
                    if ($emailToDelete === '') {
                        $error = "Invalid email address selected for deletion.";
                    } else {
                        $parts = explode('@', $emailToDelete, 2);
                        if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
                            $error = "Invalid email address selected for deletion: {$emailToDelete}.";
                        } else {
                            $userPart   = $parts[0];
                            $domainPart = $parts[1];
                            try {
                                uapi_call($cpanelDomain, $cpanelUser, $cpanelApiToken, 'Email', 'delete_pop', [
                                    'email'  => $userPart,
                                    'domain' => $domainPart,
                                ]);
                                $success = "Email account '{$emailToDelete}' deleted successfully.";
                            } catch (Exception $e) {
                                $error = "Failed to delete email account: " . h($e->getMessage());
                            }
                        }
                    }
                } elseif (isset($_POST['change_password'])) {
                    csrf_check();
                    $emailToChange = trim($_POST['email_address'] ?? '');
                    $newPassword = trim($_POST['new_password'] ?? '');
                    $confirmPassword = trim($_POST['confirm_password'] ?? '');

                    if ($emailToChange === '') {
                        $error = "Email address is required.";
                    } elseif ($newPassword === '') {
                        $error = "Password cannot be empty.";
                    } elseif ($newPassword !== $confirmPassword) {
                        $error = "Passwords do not match.";
                    } else {
                        $parts = explode('@', $emailToChange, 2);
                        if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
                            $error = "Invalid email address format.";
                        } else {
                            $userPart = $parts[0];
                            $domainPart = $parts[1];
                            try {
                                uapi_call($cpanelDomain, $cpanelUser, $cpanelApiToken, 'Email', 'passwd_pop', [
                                    'email' => $userPart,
                                    'domain' => $domainPart,
                                    'password' => $newPassword,
                                ]);
                                $success = "Password changed successfully for '{$emailToChange}'.";
                            } catch (Exception $e) {
                                $error = "Failed to change password: " . h($e->getMessage());
                            }
                        }
                    }
                } elseif ($subPage === 'add') {
                    csrf_check();
                    $usernameNew = trim($_POST['email_username'] ?? '');
                    $pwd         = $_POST['email_password'] ?? '';
                    $pwd2        = $_POST['email_password_confirm'] ?? '';

                    if ($pwd !== $pwd2) {
                        $error = "Passwords do not match.";
                    } elseif ($usernameNew === '' || $pwd === '') {
                        $error = "Email and password cannot be empty.";
                    } else {
                        $email_full = $usernameNew . '@' . $cpanelDomain;
                        try {
                            uapi_call($cpanelDomain, $cpanelUser, $cpanelApiToken, 'Email', 'add_pop', [
                                'email'    => $usernameNew,
                                'password' => $pwd,
                                'domain'   => $cpanelDomain,
                            ]);
                            $success = "Email account '{$email_full}' created successfully.";
                        } catch (Exception $e) {
                            $error = "Failed to create email account: " . h($e->getMessage());
                        }
                    }
                }
            }
            // List emails with pagination
            $perPage = 10;
            $pageNumber = max(1, (int)($_GET['p'] ?? 1));
            $offset = ($pageNumber - 1) * $perPage;

            $emails = uapi_call($cpanelDomain, $cpanelUser, $cpanelApiToken, 'Email', 'list_pops');
            $allEmails = $emails['data'] ?? [];
            $cPanelData['emails'] = array_slice($allEmails, $offset, $perPage);
            $cPanelData['totalPages'] = max(1, (int)ceil((count($allEmails) ?: 1) / $perPage));
            $cPanelData['currentPage'] = $pageNumber;
        } elseif ($page === 'ssl') {
            $perPage = 10;
            $pageNumber = max(1, (int)($_GET['p'] ?? 1));
            $offset = ($pageNumber - 1) * $perPage;

            try {
                $certs = uapi_call($cpanelDomain, $cpanelUser, $cpanelApiToken, 'SSL', 'list_certs');
                $allCerts = $certs['data'] ?? [];
                $cPanelData['certs'] = array_slice($allCerts, $offset, $perPage);
                $cPanelData['totalPages'] = max(1, (int)ceil((count($allCerts) ?: 1) / $perPage));
                $cPanelData['currentPage'] = $pageNumber;
            } catch (Exception $e) {
                $cPanelData['certs'] = [];
                $cPanelData['totalPages'] = 1;
                $cPanelData['currentPage'] = 1;
                $error .= "Could not retrieve SSL certs: " . h($e->getMessage()) . " ";
            }
        } elseif ($page === 'invoices') {
            try {
                $accessToken = $_ENV['ZOHO_ACCESS_TOKEN'] ?? '';
                if ($accessToken === '' || ZOHO_ORGANIZATION_ID === 'YOUR_ZOHO_ORG_ID_HERE') {
                    $cPanelData['invoices'] = [];
                    $error = "Zoho API credentials are not configured.";
                } else {
                    $result = zoho_call('invoices');
                    $allInvoices = $result['invoices'] ?? [];
                    $filteredInvoices = [];
                    foreach ($allInvoices as $invoice) {
                        $invoiceId = $invoice['invoice_id'] ?? null;
                        if ($invoiceId) {
                            $invoiceDetail = zoho_call('invoices/' . $invoiceId);
                            $lineItems = $invoiceDetail['invoice']['line_items'] ?? [];
                            foreach ($lineItems as $item) {
                                $itemName = strtolower($item['name'] ?? '');
                                $itemDesc = strtolower($item['description'] ?? '');
                                if (strpos($itemName, strtolower($cpanelDomain)) !== false || strpos($itemDesc, strtolower($cpanelDomain)) !== false) {
                                    $filteredInvoices[] = $invoice;
                                    break;
                                }
                            }
                        }
                    }
                    $cPanelData['invoices'] = $filteredInvoices;
                }
            } catch (Exception $e) {
                $cPanelData['invoices'] = [];
                $errorMsg = $e->getMessage();
                if (strpos($errorMsg, '401') !== false) {
                    $error = "Zoho access token has expired or is invalid. Please generate a new access token from Zoho Books API console.";
                } else {
                    $error = "Could not retrieve invoices: " . h($errorMsg);
                }
            }
        } elseif ($page === 'settings') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                csrf_check();
                $fullName = trim($_POST['full_name'] ?? '');
                $profilePictureUrl = trim($_POST['profile_picture_url'] ?? '');
                if (updateUserProfile($cpanelUser, $cpanelDomain, $fullName, $profilePictureUrl, $cpanelApiToken)) {
                    $success = "Profile updated successfully.";
                    $userProfile = getUserProfile($cpanelUser, $cpanelDomain);
                } else {
                    $error = "Failed to update profile.";
                }
            }
        } elseif ($page === 'admin' && $isSuperuser) {
            $quotes_count = count(getQuotes());
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                csrf_check();
                if (isset($_POST['add_user'])) {
                    $cpanel_username = trim($_POST['cpanel_username'] ?? '');
                    $domain = trim($_POST['domain'] ?? '');
                    $api_token = trim($_POST['api_token'] ?? '');
                    $full_name = trim($_POST['full_name'] ?? '');
                    $is_superuser_val = isset($_POST['is_superuser']) ? 1 : 0;
                    $package = trim($_POST['package'] ?? 'Package not Found');
                    $conn = getDbConnection();
                    $stmt = $conn->prepare("INSERT INTO users (cpanel_username, domain, api_token, full_name, is_superuser, package) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssis", $cpanel_username, $domain, $api_token, $full_name, $is_superuser_val, $package);
                    if ($stmt->execute()) {
                        $success = "User added successfully.";
                    } else {
                        $error = "Failed to add user: " . h($conn->error);
                    }
                    $stmt->close();
                    $conn->close();
                } elseif (isset($_POST['edit_user'])) {
                    $id = (int)($_POST['id'] ?? 0);
                    $cpanel_username = trim($_POST['cpanel_username'] ?? '');
                    $domain = trim($_POST['domain'] ?? '');
                    $api_token = trim($_POST['api_token'] ?? '');
                    $full_name = trim($_POST['full_name'] ?? '');
                    $is_superuser_val = isset($_POST['is_superuser']) ? 1 : 0;
                    $package = trim($_POST['package'] ?? 'Soloprenuer');
                    $conn = getDbConnection();
                    $stmt = $conn->prepare("UPDATE users SET cpanel_username=?, domain=?, api_token=?, full_name=?, is_superuser=?, package=? WHERE id=?");
                    $stmt->bind_param("ssssisi", $cpanel_username, $domain, $api_token, $full_name, $is_superuser_val, $package, $id);
                    if ($stmt->execute()) {
                        $success = "User updated successfully.";
                    } else {
                        $error = "Failed to update user: " . h($conn->error);
                    }
                    $stmt->close();
                    $conn->close();
                } elseif (isset($_POST['delete_user'])) {
                    $id = (int)$_POST['delete_user'];
                    $conn = getDbConnection();
                    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
                    $stmt->bind_param("i", $id);
                    if ($stmt->execute()) {
                        $success = "User deleted successfully.";
                    } else {
                        $error = "Failed to delete user: " . h($conn->error);
                    }
                    $stmt->close();
                    $conn->close();
                } elseif (isset($_POST['add_quote'])) {
                    $quote_text = trim($_POST['quote_text'] ?? '');
                    $author = trim($_POST['author'] ?? '');
                    $image_url = trim($_POST['image_url'] ?? '');
                    if ($quotes_count >= 20) {
                        $error = "Quote limit of 20 reached. Please delete an existing quote first.";
                    } elseif (addQuote($quote_text, $author, $image_url)) {
                        $success = "Quote added successfully.";
                    } else {
                        $error = "Failed to add quote.";
                    }
                } elseif (isset($_POST['edit_quote'])) {
                    $id = (int)($_POST['id'] ?? 0);
                    $quote_text = trim($_POST['quote_text'] ?? '');
                    $author = trim($_POST['author'] ?? '');
                    $image_url = trim($_POST['image_url'] ?? '');
                    if (updateQuote($id, $quote_text, $author, $image_url)) {
                        $success = "Quote updated successfully.";
                    } else {
                        $error = "Failed to update quote.";
                    }
                } elseif (isset($_POST['delete_quote'])) {
                    $id = (int)($_POST['delete_quote'] ?? 0);
                    if (deleteQuote($id)) {
                        $success = "Quote deleted successfully.";
                    } else {
                        $error = "Failed to delete quote.";
                    }
                }
                header("Location: index.php?page=admin&sub={$subPage}");
                exit;
            }

            if ($subPage === 'quotes') {
                $cPanelData['quotes'] = getQuotes();
            } else {
                $conn = getDbConnection();
                $res = $conn->query("SELECT * FROM users ORDER BY id DESC");
                $cPanelData['users'] = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
                $conn->close();
            }
        } else {
            // Non-superuser trying /admin or unknown page while logged in
            if ($page === 'admin') {
                header("Location: index.php");
                exit;
            }
        }
    } catch (Exception $e) {
        $error = "API error: " . h($e->getMessage());
        $loggedIn = false;
        session_destroy();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drift Nimbus Dashboard - <?php echo h(ucfirst($page)); ?></title>
    <link rel="icon" href="https://dashboard.driftnimbus.com/assets/images/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&family=Syne:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <?php if ($loggedIn): ?>
        <style>
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                width: 260px;
                background: #1e1e1e;
                padding: 1.5rem 0;
                overflow-y: auto;
                z-index: 1000;
            }

            .sidebar-logo {
                padding: 0 1.5rem 1.5rem;
                border-bottom: 1px solid #2d2d2d;
                margin-bottom: 1rem;
            }

            .sidebar-nav {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .sidebar-nav-item {
                margin: 0.25rem 0.75rem;
            }

            .sidebar-nav-link {
                display: flex;
                align-items: center;
                padding: 0.75rem 1rem;
                color: #b0b0b0;
                text-decoration: none;
                border-radius: 0.375rem;
                transition: all 0.2s;
                font-size: 0.95rem;
            }

            .sidebar-nav-link:hover {
                background: #2a2a2a;
                color: #ffffff;
            }

            .sidebar-nav-link.active {
                background: #3a3a3a;
                color: #ffffff;
            }

            .sidebar-nav-link i {
                width: 20px;
                margin-right: 0.75rem;
                font-size: 1rem;
            }

            .sidebar-nav-link .chevron {
                margin-left: auto;
                font-size: 0.75rem;
                transition: transform 0.2s;
            }

            .sidebar-nav-link[aria-expanded="true"] .chevron {
                transform: rotate(90deg);
            }

            .sidebar-submenu {
                list-style: none;
                padding: 0;
                margin: 0.25rem 0 0.5rem 2.5rem;
            }

            .sidebar-submenu-link {
                display: block;
                padding: 0.5rem 1rem;
                color: #909090;
                text-decoration: none;
                border-radius: 0.375rem;
                font-size: 0.9rem;
                transition: all 0.2s;
            }

            .sidebar-submenu-link:hover {
                background: #2a2a2a;
                color: #ffffff;
            }

            .sidebar-user {
                padding: 1rem 1.5rem;
                border-top: 1px solid #2d2d2d;
                margin-top: auto;
            }

            .sidebar-user-info {
                display: flex;
                align-items: center;
                color: #b0b0b0;
                text-decoration: none;
                padding: 0.5rem;
                border-radius: 0.375rem;
                transition: background 0.2s;
            }

            .sidebar-user-info:hover {
                background: #2a2a2a;
            }

            .sidebar-user-info img {
                margin-right: 0.75rem;
            }

            .main-content {
                margin-left: 260px;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }
        </style>

        <aside class="sidebar">
            <div class="sidebar-logo">
                <a href="?page=dashboard">
                    <img src="https://hosting.driftnimbus.com/wp-content/uploads/2025/02/nimbus-logo-horizontal-white.svg" alt="Drift Nimbus" style="width: 100%; max-width: 180px;">
                </a>
            </div>
            <?php if ($isSuperuser): ?>
                <div style="padding: 0 1.5rem 1rem;">
                    <label class="form-label text-white" style="font-size: 0.85rem; margin-bottom: 0.5rem;">Switch Account</label>
                    <select class="form-select form-select-sm" id="adminDomainSwitch" style="background: #2a2a2a; color: #ffffff; border: 1px solid #3a3a3a;">
                        <option value="">Select domain...</option>
                        <?php
                        $conn = getDbConnection();
                        $allUsers = $conn->query("SELECT id, cpanel_username, domain FROM users ORDER BY domain ASC");
                        while ($user = $allUsers->fetch_assoc()) {
                            $selected = ($user['domain'] === $cpanelDomain && $user['cpanel_username'] === $cpanelUser) ? 'selected' : '';
                            echo '<option value="' . h($user['id']) . '" ' . $selected . '>' . h($user['domain']) . ' (' . h($user['cpanel_username']) . ')</option>';
                        }
                        $conn->close();
                        ?>
                    </select>
                </div>
            <?php endif; ?>
            <div class="sidebar-user" style="border-bottom: 1px solid #2d2d2d; padding-bottom: 1rem; margin-bottom: 1rem;">
                <a href="?page=settings" class="sidebar-user-info">
                    <img src="<?php echo h($userProfile['profile_picture_url'] ?? 'https://placehold.co/40x40/64748b/e2e8f0?text=PFP'); ?>" class="rounded-circle" width="36" height="36" alt="Profile">
                    <div>
                        <div style="font-size: 0.9rem; color: #ffffff;"><?php echo h($userProfile['full_name'] ?? $cpanelUser); ?></div>
                        <div style="font-size: 0.75rem; color: #707070;"><?php echo h($cpanelDomain); ?></div>
                    </div>
                </a>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-nav-item">
                    <a href="?page=dashboard" class="sidebar-nav-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>">
                        <i class="fas fa-th-large"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="?page=domains" class="sidebar-nav-link <?php echo $page === 'domains' ? 'active' : ''; ?>">
                        <i class="fas fa-globe"></i>
                        <span>Domains</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="#emailsSubmenu" class="sidebar-nav-link <?php echo $page === 'emails' ? 'active' : ''; ?>" data-bs-toggle="collapse" aria-expanded="<?php echo $page === 'emails' ? 'true' : 'false'; ?>">
                        <i class="fas fa-envelope"></i>
                        <span>Emails</span>
                        <i class="fas fa-chevron-right chevron"></i>
                    </a>
                    <ul class="collapse sidebar-submenu <?php echo $page === 'emails' ? 'show' : ''; ?>" id="emailsSubmenu">
                        <li><a href="?page=emails" class="sidebar-submenu-link">Email Accounts</a></li>
                        <li><a href="?page=emails&sub=add" class="sidebar-submenu-link">Add Email</a></li>
                        <li><a href="https://mail.driftnimbus.com" target="_blank" class="sidebar-submenu-link">Nimbus Mail <i class="fas fa-external-link-alt" style="font-size: 0.75rem; margin-left: 0.25rem;"></i></a></li>
                    </ul>
                </li>
                <li class="sidebar-nav-item">
                    <a href="?page=ssl" class="sidebar-nav-link <?php echo $page === 'ssl' ? 'active' : ''; ?>">
                        <i class="fas fa-lock"></i>
                        <span>SSL Certificates</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="?page=invoices" class="sidebar-nav-link <?php echo $page === 'invoices' ? 'active' : ''; ?>">
                        <i class="fas fa-file-invoice"></i>
                        <span>Billing</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="?page=settings" class="sidebar-nav-link <?php echo $page === 'settings' ? 'active' : ''; ?>">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>
                <?php if ($isSuperuser): ?>
                    <li class="sidebar-nav-item">
                        <a href="#adminSubmenu" class="sidebar-nav-link <?php echo $page === 'admin' ? 'active' : ''; ?>" data-bs-toggle="collapse" aria-expanded="<?php echo $page === 'admin' ? 'true' : 'false'; ?>">
                            <i class="fas fa-user-shield"></i>
                            <span>Admin</span>
                            <i class="fas fa-chevron-right chevron"></i>
                        </a>
                        <ul class="collapse sidebar-submenu <?php echo $page === 'admin' ? 'show' : ''; ?>" id="adminSubmenu">
                            <li><a href="?page=admin" class="sidebar-submenu-link">Manage Users</a></li>
                            <li><a href="?page=admin&sub=quotes" class="sidebar-submenu-link">Manage Quotes</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
                <li class="sidebar-nav-item">
                    <a href="?action=logout" class="sidebar-nav-link">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Log Off</span>
                    </a>
                </li>
            </ul>
        </aside>

        <div class="main-content">
            <main class="flex-grow-1 p-3 p-md-5">
                <div class="container-fluid">
                    <br>

                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo h($success); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo h($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($page === 'dashboard'): ?>
                        <div class="bento-grid">
                            <!-- Welcome Card -->
                            <div class="card wide p-4" style="background-image: url('https://dashboard.driftnimbus.com/assets/images/main.jpg'); background-size: cover; background-position: center;">
                                <div class="card-body">
                                    <h1 class="text-white fw-bold mb-2">Welcome, <br><?php echo h($cPanelData['welcomeName']); ?></h1>
                                    <p class="text-white mb-1">Server Status: <span style="color: #28a745; font-weight: bold;">Online</span></p>
                                    <p class="text-white mb-0">Active Package: <?php echo h($userProfile['package'] ?? 'N/A'); ?></p>
                                </div>
                            </div>
                            <!-- Time Widget -->
                            <div class="card p-4 text-center d-flex flex-column align-items-center justify-content-center" style="background-image: url('https://dashboard.driftnimbus.com/assets/images/bg-themes/cardbg2.jpg'); background-size: cover; background-position: center;">
                                <div class="card-body w-100">
                                    <h5 class="card-title text-highlight text-white">Current Time</h5>
                                    <div class="time-widget text-white" id="time-widget"></div>
                                    <p class="text-white">Harare, Zimbabwe</p>
                                </div>
                            </div>
                            <!-- Weather Widget -->
                            <?php
                            $weather_code = $cPanelData['weather']['weather_code'] ?? null;
                            $temperature = $cPanelData['weather']['temperature_2m'] ?? 'N/A';
                            $weather_description = 'N/A';
                            $weather_icon = '';
                            $weather_bg = 'assets/images/weather/cloudy.jpg';

                            switch ($weather_code) {
                                case 0:
                                    $weather_description = 'Sunny';
                                    $weather_icon = 'assets/images/weather/sunny.png';
                                    $weather_bg = 'assets/images/weather/sunny.jpg';
                                    break;
                                case 1:
                                case 2:
                                    $weather_description = 'Partially Cloudy';
                                    $weather_icon = 'assets/images/weather/partially-cloudy.png';
                                    $weather_bg = 'assets/images/weather/cloudy.jpg';
                                    break;
                                case 3:
                                    $weather_description = 'Cloudy';
                                    $weather_icon = 'assets/images/weather/partial-clouds.png';
                                    $weather_bg = 'assets/images/weather/cloudy.jpg';
                                    break;
                                case 45:
                                case 48:
                                    $weather_description = 'Fog';
                                    $weather_icon = 'assets/images/weather/partial-clouds.png';
                                    $weather_bg = 'assets/images/weather/cloudy.jpg';
                                    break;
                                case 51:
                                case 53:
                                case 55:
                                    $weather_description = 'Light Showers';
                                    $weather_icon = 'assets/images/weather/light-showers.png';
                                    $weather_bg = 'assets/images/weather/rainy.jpg';
                                    break;
                                case 61:
                                case 63:
                                case 65:
                                case 80:
                                case 81:
                                case 82:
                                    $weather_description = 'Rainy';
                                    $weather_icon = 'assets/images/weather/rain.png';
                                    $weather_bg = 'assets/images/weather/rainy.jpg';
                                    break;
                                case 56:
                                case 57:
                                case 66:
                                case 67:
                                    $weather_description = 'Freezing Rain';
                                    $weather_icon = 'assets/images/weather/rain.png';
                                    $weather_bg = 'assets/images/weather/rainy.jpg';
                                    break;
                                case 71:
                                case 73:
                                case 75:
                                case 77:
                                case 85:
                                case 86:
                                    $weather_description = 'Snow';
                                    $weather_icon = 'assets/images/weather/light-showers.png';
                                    $weather_bg = 'assets/images/weather/cloudy.jpg';
                                    break;
                                case 95:
                                case 96:
                                case 99:
                                    $weather_description = 'Thunderstorm';
                                    $weather_icon = 'assets/images/weather/thunderstorms.png';
                                    $weather_bg = 'assets/images/weather/thunderstorm.jpg';
                                    break;
                            }
                            ?>
                            <div class="card p-4 text-center d-flex flex-column align-items-center justify-content-center" style="background-image: url('<?php echo h($weather_bg); ?>'); background-size: cover; background-position: center;">
                                <div class="card-body w-100">
                                    <h5 class="card-title text-highlight text-white">Weather</h5>
                                    <?php if ($weather_code !== null): ?>
                                        <img src="<?php echo h($weather_icon); ?>" alt="Weather" style="width: 80px; height: 80px;">
                                        <h4 class="mb-0 mt-2 text-white"><?php echo h($temperature); ?>°C</h4>
                                        <p class="text-white"><?php echo h($weather_description); ?></p>
                                    <?php else: ?>
                                        <p class="text-white">Weather unavailable</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!-- Disk Usage Card -->
                            <div class="card p-4 text-center d-flex flex-column align-items-center justify-content-center card-bg2">
                                <div class="card-body w-100">
                                    <h5 class="card-title text-highlight text-white">Disk Usage</h5>
                                    <i class="fas fa-hdd text-white" style="font-size: 3rem;"></i>
                                    <?php if ($cPanelData['diskUsage'] !== null): ?>
                                        <h4 class="mb-0 mt-2 text-white"><?php echo h($cPanelData['diskUsage']); ?> GB</h4>
                                        <p class="text-white">Used Space</p>
                                    <?php else: ?>
                                        <h4 class="mb-0 mt-2 text-white">N/A</h4>
                                        <p class="text-white">Data Unavailable</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!-- SSL Notification Card -->
                            <div class="card p-4 text-center d-flex flex-column align-items-center justify-content-center card-bg2">
                                <div class="card-body w-100">
                                    <h5 class="card-title text-highlight text-white" style="color: var(--ssl-green) !important;">SSL Protected!</h5>
                                    <i class="fas fa-lock text-white" style="font-size: 3rem;"></i>
                                    <h4 class="mb-0 mt-2 text-white">Secure</h4>
                                    <p class="text-white">Your connection is safe</p>
                                    <a href="?page=ssl" class="btn btn-primary mt-3">View Certificates</a>
                                </div>
                            </div>
                            <!-- Quote Widget -->
                            <div class="card wide p-4 d-flex flex-column justify-content-center quote-card" style="<?php echo !empty($cPanelData['quote']['image_url']) ? 'background-image: url(' . h($cPanelData['quote']['image_url']) . ')' : 'background-image: url(https://dashboard.driftnimbus.com/assets/images/quote.jpg)'; ?>">
                                <div class="quote-overlay"></div>
                                <div class="card-body quote-content">
                                    <h5 class="card-title text-highlight text-white">Quote of the Day</h5>
                                    <?php if (!empty($cPanelData['quote'])): ?>
                                        <p class="text-white fst-italic" style="font-size: 1.5rem; margin-top: 30px;">"<?php echo h($cPanelData['quote']['quote_text']); ?>"</p>
                                        <footer class="blockquote-footer text-white mt-2">
                                            <cite><?php echo h($cPanelData['quote']['author']); ?></cite>
                                        </footer>
                                    <?php else: ?>
                                        <p class="text-white">Quote unavailable.</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>

                    <?php elseif ($page === 'domains'): ?>
                        <div class="bento-grid">
                            <div class="card full-width p-4">
                                <div class="card-body">
                                    <h2 class="card-title text-white mb-4">Domains</h2>
                                    <div class="table-responsive">
                                        <table class="table-custom">
                                            <thead>
                                                <tr>
                                                    <th>Domain</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($cPanelData['domains'] as $domain): ?>
                                                    <tr>
                                                        <td><?php echo h($domain); ?></td>
                                                        <td><a href="https://cpanel.<?php echo h($domain); ?>" target="_blank" class="btn btn-sm btn-primary me-2">Edit in cPanel <i class="fas fa-external-link-alt"></i></a></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php elseif ($page === 'emails'): ?>
                        <?php if ($subPage === 'add'): ?>
                            <div class="bento-grid">
                                <div class="card wide p-4">
                                    <div class="card-body">
                                        <h2 class="card-title text-highlight text-white mb-4">Add Email Account</h2>
                                        <form method="POST" action="?page=emails&sub=add">
                                            <?php csrf_field(); ?>
                                            <input type="hidden" name="add_email" value="1">
                                            <div class="mb-3">
                                                <label for="email_username" class="form-label text-white">Username</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="email_username" name="email_username" placeholder="username" required>
                                                    <span class="input-group-text">@<?php echo h($cpanelDomain); ?></span>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="email_password" class="form-label text-white">Password</label>
                                                <input type="password" class="form-control" id="email_password" name="email_password" placeholder="Password" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="email_password_confirm" class="form-label text-white">Confirm Password</label>
                                                <input type="password" class="form-control" id="email_password_confirm" name="email_password_confirm" placeholder="Confirm Password" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100">Add Account</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="card p-4">
                                    <div class="card-body">
                                        <h2 class="card-title text-highlight text-white mb-4">Recently Created</h2>
                                        <?php
                                        try {
                                            $emails = uapi_call($cpanelDomain, $cpanelUser, $cpanelApiToken, 'Email', 'list_pops');
                                            $allEmails = $emails['data'] ?? [];
                                            $recentEmails = array_slice($allEmails, 0, 5);
                                            if (empty($allEmails)) {
                                                echo '<p class="text-white">You have no e-mail addresses. Add a new address to show listing.</p>';
                                            } else {
                                                echo '<div class="table-responsive"><table class="table-custom"><tbody>';
                                                foreach ($recentEmails as $email) {
                                                    echo '<tr><td>' . h($email['email'] ?? '') . '</td></tr>';
                                                }
                                                echo '</tbody></table></div>';
                                                if (count($allEmails) > 5) {
                                                    echo '<a href="?page=emails" class="btn btn-primary w-100 mt-3">View all Addresses</a>';
                                                }
                                            }
                                        } catch (Exception $e) {
                                            echo '<p class="text-warning">Unable to load recent emails: ' . h($e->getMessage()) . '</p>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="bento-grid">
                                <div class="card full-width p-4">
                                    <div class="card-body">
                                        <h2 class="card-title text-highlight text-white mb-4">Email Accounts</h2>
                                        <div class="table-responsive">
                                            <table class="table-custom">
                                                <thead>
                                                    <tr>
                                                        <th>Email</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($cPanelData['emails'])): ?>
                                                        <?php foreach ($cPanelData['emails'] as $email): ?>
                                                            <?php
                                                            $emailAddress = trim($email['email'] ?? '');
                                                            $emailUser    = trim($email['user'] ?? '');
                                                            $emailDomain  = trim($email['domain'] ?? $cpanelDomain);

                                                            if ($emailAddress === '' && $emailUser !== '' && $emailDomain !== '') {
                                                                $emailAddress = $emailUser . '@' . $emailDomain;
                                                            } elseif ($emailAddress !== '' && strpos($emailAddress, '@') === false && $emailDomain !== '') {
                                                                $emailAddress .= '@' . $emailDomain;
                                                            }

                                                            $emailAddress = trim($emailAddress);
                                                            ?>
                                                            <tr>
                                                                <td><?php echo h($emailAddress); ?></td>
                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-warning me-2"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#changePasswordModal"
                                                                        data-email="<?php echo h($emailAddress); ?>">
                                                                        Change Password
                                                                    </button>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-danger"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#deleteEmailModal"
                                                                        data-email="<?php echo h($emailAddress); ?>">
                                                                        Delete
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="2" class="text-center text-white">No email accounts found.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>

                                            </table>
                                        </div>
                                        <nav class="mt-4">
                                            <ul class="pagination justify-content-center">
                                                <?php for ($i = 1; $i <= $cPanelData['totalPages']; $i++): ?>
                                                    <li class="page-item <?php echo $cPanelData['currentPage'] == $i ? 'active' : ''; ?>">
                                                        <a class="page-link" href="?page=emails&p=<?php echo $i; ?>"><?php echo $i; ?></a>
                                                    </li>
                                                <?php endfor; ?>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form method="POST" action="?page=emails" class="modal-content">
                                        <?php csrf_field(); ?>
                                        <input type="hidden" name="change_password" value="1">
                                        <input type="hidden" name="email_address" id="change-password-email-address">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="text-white">Change password for <span class="fw-bold" id="change-password-email-label">unknown email account</span></p>
                                            <div class="mb-3">
                                                <label for="new_password" class="form-label text-white">New Password</label>
                                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="confirm_password" class="form-label text-white">Confirm Password</label>
                                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Change Password</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="modal fade" id="deleteEmailModal" tabindex="-1" aria-labelledby="deleteEmailModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form method="POST" action="?page=emails" class="modal-content">
                                        <?php csrf_field(); ?>
                                        <input type="hidden" name="delete_email" value="1">
                                        <input type="hidden" name="email_address" id="delete-email-address">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteEmailModalLabel">Delete Email Account</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to delete <span class="fw-bold" id="delete-email-address-label">unknown email account</span>? This action cannot be undone.</p>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <script>
                                const changePasswordModal = document.getElementById('changePasswordModal');
                                if (changePasswordModal) {
                                    changePasswordModal.addEventListener('show.bs.modal', function(event) {
                                        const button = event.relatedTarget;
                                        const rawEmail = (button && button.getAttribute('data-email')) ? button.getAttribute('data-email').trim() : '';
                                        console.log('Email from button:', rawEmail);
                                        const input = changePasswordModal.querySelector('#change-password-email-address');
                                        if (input) {
                                            input.value = rawEmail;
                                            console.log('Input value set to:', input.value);
                                        }
                                        const label = changePasswordModal.querySelector('#change-password-email-label');
                                        if (label) {
                                            label.textContent = rawEmail !== '' ? rawEmail : 'unknown email account';
                                        }
                                    });
                                }

                                const deleteEmailModal = document.getElementById('deleteEmailModal');
                                if (deleteEmailModal) {
                                    deleteEmailModal.addEventListener('show.bs.modal', function(event) {
                                        const button = event.relatedTarget;
                                        const rawEmail = (button && button.getAttribute('data-email')) ? button.getAttribute('data-email').trim() : '';
                                        const input = deleteEmailModal.querySelector('#delete-email-address');
                                        if (input) {
                                            input.value = rawEmail;
                                        }
                                        const label = deleteEmailModal.querySelector('#delete-email-address-label');
                                        if (label) {
                                            label.textContent = rawEmail !== '' ? rawEmail : 'unknown email account';
                                        }
                                    });
                                }
                            </script>

                        <?php endif; ?>

                    <?php elseif ($page === 'ssl'): ?>
                        <div class="bento-grid">
                            <div class="card full-width p-4">
                                <div class="card-body">
                                    <h2 class="card-title text-highlight text-white mb-4">SSL Certificates</h2>
                                    <div class="table-responsive">
                                        <table class="table-custom">
                                            <thead>
                                                <tr>
                                                    <th>Domain</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($cPanelData['certs'] as $cert): ?>
                                                    <tr>
                                                        <td><?php echo h($cert['domains'][0] ?? 'N/A'); ?></td>
                                                        <td><a href="https://cpanel.<?php echo h($cpanelDomain); ?>" target="_blank" class="btn btn-sm btn-primary">Edit in cPanel® <i class="fas fa-external-link-alt"></i></a></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <nav class="mt-4">
                                        <ul class="pagination justify-content-center">
                                            <?php for ($i = 1; $i <= $cPanelData['totalPages']; $i++): ?>
                                                <li class="page-item <?php echo $cPanelData['currentPage'] == $i ? 'active' : ''; ?>">
                                                    <a class="page-link" href="?page=ssl&p=<?php echo $i; ?>"><?php echo $i; ?></a>
                                                </li>
                                            <?php endfor; ?>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>

                    <?php elseif ($page === 'invoices'): ?>
                        <div class="bento-grid">
                            <div class="card full-width p-4">
                                <div class="card-body">
                                    <h2 class="card-title text-highlight text-white mb-4">Billing</h2>
                                    <div class="table-responsive">
                                        <table class="table-custom">
                                            <thead>
                                                <tr>
                                                    <th>Invoice #</th>
                                                    <th>Date</th>
                                                    <th>Due Date</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($cPanelData['invoices'])): ?>
                                                    <?php foreach ($cPanelData['invoices'] as $invoice): ?>
                                                        <?php
                                                        $invoiceNumber = $invoice['invoice_number'] ?? 'N/A';
                                                        $amount = $invoice['balance'] ?? $invoice['total'] ?? 0;
                                                        $args = 'search=' . urlencode('billing@driftnimbus.com') . '&amount=' . $amount . '&reference=' . urlencode($invoiceNumber) . '&l=1';
                                                        $base64 = base64_encode($args);
                                                        $paymentUrl = 'https://www.paynow.co.zw/payment/link/?q=' . urlencode($base64);
                                                        ?>
                                                        <tr>
                                                            <td><?php echo h($invoiceNumber); ?></td>
                                                            <td><?php echo h($invoice['date'] ?? 'N/A'); ?></td>
                                                            <td><?php echo h($invoice['due_date'] ?? 'N/A'); ?></td>
                                                            <td><?php echo h($invoice['total'] ?? 'N/A'); ?></td>
                                                            <td>
                                                                <span class="badge bg-<?php echo ($invoice['balance'] > 0) ? 'danger' : 'success'; ?>">
                                                                    <?php echo ($invoice['balance'] > 0) ? 'UNPAID' : 'PAID'; ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <?php if ($invoice['balance'] > 0): ?>
                                                                    <a href="<?php echo h($paymentUrl); ?>" target="_blank" class="btn btn-sm btn-success">Make Payment</a>
                                                                <?php else: ?>
                                                                    <span class="text-success">Paid</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center">
                                                            <?php echo h($error); ?>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php elseif ($page === 'settings'): ?>
                        <div class="bento-grid">
                            <div class="card full-width p-4">
                                <div class="card-body">
                                    <h2 class="card-title text-highlight text-white mb-4">Settings</h2>
                                    <form method="POST" action="?page=settings">
                                        <?php csrf_field(); ?>
                                        <div class="mb-3 d-flex flex-column flex-md-row align-items-center">
                                            <div class="me-md-4 mb-3 mb-md-0">
                                                <img src="<?php echo h($userProfile['profile_picture_url'] ?? 'https://placehold.co/90x90/e2e8f0/64748b?text=PFP'); ?>" class="rounded-circle p-1" width="90" height="90" alt="Profile Picture">
                                            </div>
                                            <div class="flex-grow-1">
                                                <label for="profile_picture_url" class="form-label text-white">Profile Picture URL</label>
                                                <input type="url" id="profile_picture_url" name="profile_picture_url" value="<?php echo h($userProfile['profile_picture_url'] ?? ''); ?>" placeholder="https://example.com/image.jpg" class="form-control">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="full_name" class="form-label text-white">Full Name</label>
                                            <input type="text" id="full_name" name="full_name" value="<?php echo h($userProfile['full_name'] ?? ''); ?>" placeholder="e.g., John Doe" class="form-control">
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">Save Changes</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    <?php elseif ($page === 'admin' && $isSuperuser): ?>
                        <div class="bento-grid">
                            <div class="card full-width p-4">
                                <div class="card-body">
                                    <h2 class="card-title text-highlight text-white mb-4">Admin: <?php echo h($subPage === 'quotes' ? 'Manage Quotes' : 'Manage Users'); ?></h2>

                                    <ul class="nav nav-tabs mb-4">
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo $subPage !== 'quotes' ? 'active' : ''; ?>" href="?page=admin">Manage Users</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo $subPage === 'quotes' ? 'active' : ''; ?>" href="?page=admin&sub=quotes">Manage Quotes</a>
                                        </li>
                                    </ul>

                                    <?php if ($subPage === 'quotes'): ?>
                                        <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#addQuoteModal">Add New Quote</button>
                                        <div class="table-responsive">
                                            <table class="table-custom">
                                                <thead>
                                                    <tr>
                                                        <th>Quote</th>
                                                        <th>Author</th>
                                                        <th>Image URL</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($cPanelData['quotes'] as $quote): ?>
                                                        <tr>
                                                            <td><?php echo h($quote['quote_text']); ?></td>
                                                            <td><?php echo h($quote['author']); ?></td>
                                                            <td><?php echo h($quote['image_url'] ?? 'N/A'); ?></td>
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-primary"
                                                                    data-bs-toggle="modal" data-bs-target="#editQuoteModal"
                                                                    data-id="<?php echo h($quote['id']); ?>"
                                                                    data-text="<?php echo h($quote['quote_text']); ?>"
                                                                    data-author="<?php echo h($quote['author']); ?>"
                                                                    data-imageurl="<?php echo h($quote['image_url']); ?>">Edit</button>
                                                                <button type="button" class="btn btn-sm btn-danger"
                                                                    data-bs-toggle="modal" data-bs-target="#deleteQuoteModal"
                                                                    data-id="<?php echo h($quote['id']); ?>">Delete</button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#addUserModal">Add New User</button>
                                        <div class="table-responsive">
                                            <table class="table-custom">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Username</th>
                                                        <th>Domain</th>
                                                        <th>Package</th>
                                                        <th>Superuser</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($cPanelData['users'] as $user): ?>
                                                        <tr>
                                                            <td><?php echo h($user['id']); ?></td>
                                                            <td><?php echo h($user['cpanel_username']); ?></td>
                                                            <td><?php echo h($user['domain']); ?></td>
                                                            <td><?php echo h($user['package'] ?? 'N/A'); ?></td>
                                                            <td><?php echo $user['is_superuser'] ? 'Yes' : 'No'; ?></td>
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-primary"
                                                                    data-bs-toggle="modal" data-bs-target="#editUserModal"
                                                                    data-id="<?php echo h($user['id']); ?>"
                                                                    data-username="<?php echo h($user['cpanel_username']); ?>"
                                                                    data-domain="<?php echo h($user['domain']); ?>"
                                                                    data-apitoken="<?php echo h($user['api_token']); ?>"
                                                                    data-fullname="<?php echo h($user['full_name']); ?>"
                                                                    data-superuser="<?php echo h($user['is_superuser']); ?>"
                                                                    data-package="<?php echo h($user['package'] ?? 'Soloprenuer'); ?>">Edit</button>
                                                                <button type="button" class="btn btn-sm btn-danger"
                                                                    data-bs-toggle="modal" data-bs-target="#deleteUserModal"
                                                                    data-id="<?php echo h($user['id']); ?>">Delete</button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Add User Modal -->
                        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-white" id="addUserModalLabel">Add New User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="POST" action="?page=admin">
                                        <?php csrf_field(); ?>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="cpanel_username" class="form-label text-white">cPanel Username</label>
                                                <input type="text" class="form-control" name="cpanel_username" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="domain" class="form-label text-white">Domain</label>
                                                <input type="text" class="form-control" name="domain" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="api_token" class="form-label text-white">API Token</label>
                                                <input type="text" class="form-control" name="api_token" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="full_name" class="form-label text-white">Full Name</label>
                                                <input type="text" class="form-control" name="full_name">
                                            </div>
                                            <div class="mb-3">
                                                <label for="package" class="form-label text-white">Package</label>
                                                <select class="form-select" name="package" required>
                                                    <option value="Soloprenuer" selected>Soloprenuer</option>
                                                    <option value="Small Business">Small Business</option>
                                                    <option value="Enterprise">Enterprise</option>
                                                </select>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1" id="is_superuser" name="is_superuser">
                                                <label class="form-check-label text-white" for="is_superuser">Is Superuser?</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <input type="hidden" name="add_user" value="1">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Add User</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Edit User Modal -->
                        <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-white" id="editUserModalLabel">Edit User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="POST" action="?page=admin">
                                        <?php csrf_field(); ?>
                                        <input type="hidden" name="id" id="edit-id">
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="edit-cpanel_username" class="form-label text-white">cPanel Username</label>
                                                <input type="text" class="form-control" id="edit-cpanel_username" name="cpanel_username" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit-domain" class="form-label text-white">Domain</label>
                                                <input type="text" class="form-control" id="edit-domain" name="domain" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit-api_token" class="form-label text-white">API Token</label>
                                                <input type="text" class="form-control" id="edit-api_token" name="api_token" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit-full_name" class="form-label text-white">Full Name</label>
                                                <input type="text" class="form-control" id="edit-full_name" name="full_name">
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit-package" class="form-label text-white">Package</label>
                                                <select class="form-select" id="edit-package" name="package" required>
                                                    <option value="Soloprenuer">Soloprenuer</option>
                                                    <option value="Small Business">Small Business</option>
                                                    <option value="Enterprise">Enterprise</option>
                                                </select>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1" id="edit-is_superuser" name="is_superuser">
                                                <label class="form-check-label text-white" for="edit-is_superuser">Is Superuser?</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" name="edit_user" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Delete User Modal -->
                        <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-white" id="deleteUserModalLabel">Confirm Deletion</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="POST" action="?page=admin">
                                        <?php csrf_field(); ?>
                                        <div class="modal-body text-white">
                                            Are you sure you want to delete this user? This action cannot be undone.
                                            <input type="hidden" name="delete_user" id="delete-user-id">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Add Quote Modal -->
                        <div class="modal fade" id="addQuoteModal" tabindex="-1" aria-labelledby="addQuoteModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-white" id="addQuoteModalLabel">Add New Quote</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="POST" action="?page=admin&sub=quotes">
                                        <?php csrf_field(); ?>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="quote_text" class="form-label text-white">Quote Text</label>
                                                <textarea class="form-control" id="quote_text" name="quote_text" rows="3" required></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="author" class="form-label text-white">Author</label>
                                                <input type="text" class="form-control" id="author" name="author" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="image_url" class="form-label text-white">Image URL (optional)</label>
                                                <input type="url" class="form-control" id="image_url" name="image_url">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <input type="hidden" name="add_quote" value="1">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Add Quote</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Quote Modal -->
                        <div class="modal fade" id="editQuoteModal" tabindex="-1" aria-labelledby="editQuoteModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-white" id="editQuoteModalLabel">Edit Quote</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="POST" action="?page=admin&sub=quotes">
                                        <?php csrf_field(); ?>
                                        <input type="hidden" name="id" id="edit-quote-id">
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="edit-quote_text" class="form-label text-white">Quote Text</label>
                                                <textarea class="form-control" id="edit-quote_text" name="quote_text" rows="3" required></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit-author" class="form-label text-white">Author</label>
                                                <input type="text" class="form-control" id="edit-author" name="author" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit-image_url" class="form-label text-white">Image URL (optional)</label>
                                                <input type="url" class="form-control" id="edit-image_url" name="image_url">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" name="edit_quote" value="1" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Quote Modal -->
                        <div class="modal fade" id="deleteQuoteModal" tabindex="-1" aria-labelledby="deleteQuoteModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-white" id="deleteQuoteModalLabel">Confirm Deletion</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="POST" action="?page=admin&sub=quotes">
                                        <?php csrf_field(); ?>
                                        <div class="modal-body text-white">
                                            Are you sure you want to delete this quote? This action cannot be undone.
                                            <input type="hidden" name="delete_quote" id="delete-quote-id">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <script>
                            document.getElementById('editUserModal')?.addEventListener('show.bs.modal', function(event) {
                                const button = event.relatedTarget;
                                const id = button.getAttribute('data-id');
                                const username = button.getAttribute('data-username');
                                const domain = button.getAttribute('data-domain');
                                const apiToken = button.getAttribute('data-apitoken');
                                const fullName = button.getAttribute('data-fullname');
                                const isSuperuser = button.getAttribute('data-superuser');
                                const package = button.getAttribute('data-package');

                                document.getElementById('edit-id').value = id;
                                document.getElementById('edit-cpanel_username').value = username;
                                document.getElementById('edit-domain').value = domain;
                                document.getElementById('edit-api_token').value = apiToken;
                                document.getElementById('edit-full_name').value = fullName;
                                document.getElementById('edit-is_superuser').checked = (parseInt(isSuperuser, 10) === 1);
                                document.getElementById('edit-package').value = package;
                            });

                            document.getElementById('deleteUserModal')?.addEventListener('show.bs.modal', function(event) {
                                const button = event.relatedTarget;
                                const id = button.getAttribute('data-id');
                                document.getElementById('delete-user-id').value = id;
                            });

                            document.getElementById('editQuoteModal')?.addEventListener('show.bs.modal', function(event) {
                                const button = event.relatedTarget;
                                const id = button.getAttribute('data-id');
                                const text = button.getAttribute('data-text');
                                const author = button.getAttribute('data-author');
                                const imageUrl = button.getAttribute('data-imageurl');
                                document.getElementById('edit-quote-id').value = id;
                                document.getElementById('edit-quote_text').value = text;
                                document.getElementById('edit-author').value = author;
                                document.getElementById('edit-image_url').value = imageUrl;
                            });



                            document.getElementById('deleteQuoteModal')?.addEventListener('show.bs.modal', function(event) {
                                const button = event.relatedTarget;
                                const id = button.getAttribute('data-id');
                                document.getElementById('delete-quote-id').value = id;
                            });
                        </script>

                    <?php elseif ($page === 'payment-success'): ?>
                        <div class="bento-grid">
                            <div class="card full-width p-4 text-center">
                                <div class="card-body">
                                    <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                                    <h2 class="card-title text-white mt-4 mb-3">Payment Successful!</h2>
                                    <p class="text-white mb-4">Thank you for your payment. Your transaction has been completed successfully.</p>
                                    <a href="?page=invoices" class="btn btn-primary">View Invoices</a>
                                </div>
                            </div>
                        </div>

                    <?php elseif ($page === 'payment-failed'): ?>
                        <div class="bento-grid">
                            <div class="card full-width p-4 text-center">
                                <div class="card-body">
                                    <i class="fas fa-times-circle text-danger" style="font-size: 5rem;"></i>
                                    <h2 class="card-title text-white mt-4 mb-3">Payment Failed</h2>
                                    <p class="text-white mb-4">Unfortunately, your payment could not be processed. Please try again or contact support.</p>
                                    <a href="?page=invoices" class="btn btn-primary">Back to Invoices</a>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>
                        <div class="alert alert-warning">Unknown page.</div>
                    <?php endif; ?>
                </div>
            </main>
        </div>

        <footer class="text-center py-3 navbar-top mt-auto">
            <div class="container-fluid">
                <span class="text-white">© <?php echo date('Y'); ?>. Powered By Drift Nimbus.</span>
            </div>
        </footer>

    <?php else: ?>
        <!-- -------------------- LOGIN PAGE (outer else) -------------------- -->
        <div class="d-flex justify-content-center align-items-center" style="min-height: 100vh; background-color: #252427;">
            <div class="card my-5 col-lg-8 mx-auto login-card rounded-4 p-0">
                <div class="row g-0">
                    <div class="col-lg-6 d-flex flex-column justify-content-center p-5">
                        <a href="https://hosting.driftnimbus.com"><img src="https://hosting.driftnimbus.com/wp-content/uploads/2025/02/nimbus-logo-horizontal-white.svg" class="mb-4" width="145" alt="Drift Nimbus Logo"></a>
                        <h4 class="fw-bold text-white">Dashboard</h4>
                        <p class="mb-5 text-white">Log in to manage your hosting account powered by Drift Nimbus.</p>
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert"><?php echo h($error); ?></div>
                        <?php endif; ?>
                        <div class="form-body mt-4">
                            <form class="row g-3" action="?page=login" method="POST">
                                <?php csrf_field(); ?>
                                <div class="col-12">
                                    <label for="domain" class="form-label text-white">Domain</label>
                                    <input type="text" class="form-control" id="domain" name="domain" placeholder="yourdomain.com" required>
                                </div>
                                <div class="col-12">
                                    <label for="username" class="form-label text-white">cPanel Username</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                <div class="col-12">
                                    <label for="password" class="form-label text-white">cPanel Password</label>
                                    <div class="input-group" id="show_hide_password">
                                        <input type="password" class="form-control border-end-0" id="password" name="password" placeholder="Enter Password" required>
                                        <a href="javascript:;" class="input-group-text bg-transparent text-white"><i class="fas fa-eye"></i></a>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-grid"><button type="submit" class="btn btn-primary">Login</button></div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-6 d-none d-lg-block login-img-col">
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//code.tidio.co/agjwvfyqtdf2zxvwva8yijqjkymuprf1.js" async></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const showHidePassword = document.getElementById('show_hide_password');
            if (showHidePassword) {
                showHidePassword.querySelector('a').addEventListener('click', function(event) {
                    event.preventDefault();
                    const passwordInput = showHidePassword.querySelector('input');
                    const icon = showHidePassword.querySelector('i');
                    if (passwordInput.type === 'text') {
                        passwordInput.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    } else {
                        passwordInput.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    }
                });
            }

            function updateTime() {
                const now = new Date();
                const hours = now.getHours().toString().padStart(2, '0');
                const minutes = now.getMinutes().toString().padStart(2, '0');
                const seconds = now.getSeconds().toString().padStart(2, '0');
                const timeString = `${hours}:${minutes}:${seconds}`;
                const timeWidget = document.getElementById('time-widget');
                if (timeWidget) {
                    timeWidget.textContent = timeString;
                }
            }

            updateTime();
            setInterval(updateTime, 1000);

            const adminDomainSwitch = document.getElementById('adminDomainSwitch');
            if (adminDomainSwitch) {
                adminDomainSwitch.addEventListener('change', function() {
                    if (this.value) {
                        window.location.href = '?switch_domain=' + this.value;
                    }
                });
            }
        });
    </script>

</body>

</html>