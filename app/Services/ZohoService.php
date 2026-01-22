<?php

class ZohoService
{
    private $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/zoho.php';
    }

    public function refreshToken()
    {
        $url = 'https://accounts.zoho.com/oauth/v2/token';
        $data = [
            'refresh_token' => $this->config['refresh_token'],
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'grant_type' => 'refresh_token'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        if (isset($result['access_token'])) {
            $this->config['access_token'] = $result['access_token'];
            $this->updateEnvToken($result['access_token']);
            return $result['access_token'];
        }
        return null;
    }

    private function updateEnvToken($token)
    {
        $envFile = __DIR__ . '/../../.env';
        $content = file_get_contents($envFile);
        $content = preg_replace('/ZOHO_ACCESS_TOKEN=.*/', "ZOHO_ACCESS_TOKEN={$token}", $content);
        file_put_contents($envFile, $content);
    }

    public function call($endpoint, $method = 'GET', $data = null)
    {
        $url = "https://www.zohoapis.com/books/v3/{$endpoint}";
        $url .= (strpos($url, '?') ? '&' : '?') . "organization_id={$this->config['organization_id']}";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Zoho-oauthtoken {$this->config['access_token']}",
            "Content-Type: application/json"
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 401) {
            $this->refreshToken();
            return $this->call($endpoint, $method, $data);
        }

        return json_decode($response, true);
    }

    public function getContactId($email, $domain)
    {
        // Try searching by Email first (most accurate)
        if (!empty($email)) {
            $response = $this->call("contacts?email_contains=" . urlencode($email));
            if (!empty($response['contacts'])) {
                return $response['contacts'][0]['contact_id'];
            }
        }

        // Try searching by Domain/Company Name
        if (!empty($domain)) {
            $response = $this->call("contacts?company_name_contains=" . urlencode($domain));
            if (!empty($response['contacts'])) {
                return $response['contacts'][0]['contact_id'];
            }
        }

        return null;
    }

    public function getInvoices($domain, $email = null)
    {
        // First try to resolve the specific contact
        $contactId = $this->getContactId($email, $domain);

        if ($contactId) {
            $response = $this->call("invoices?customer_id={$contactId}");
        } else {
            // Fallback to legacy loose search
            $response = $this->call("invoices?customer_name=" . urlencode($domain));
        }

        return $response['invoices'] ?? [];
    }
}
