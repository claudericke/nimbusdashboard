<?php

class CpanelService {
    private $host;
    private $port;
    private $username;
    private $token;

    public function __construct($username = null, $token = null) {
        $config = require __DIR__ . '/../../config/cpanel.php';
        $this->host = $config['host'];
        $this->port = $config['port'];
        $this->username = $username ?? Session::getUsername();
        $this->token = $token ?? Session::getApiToken();
    }

    public function createToken($username, $password) {
        $url = "https://{$this->host}:{$this->port}/execute/Tokens/create_token";
        $query = http_build_query(['label' => 'DriftNimbusDashboard']);
        $ch = curl_init("{$url}?{$query}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('cURL error: ' . $error);
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true);
        if ($httpCode === 200 && isset($data['status']) && intval($data['status']) === 1) {
            return $data['data'][0]['token'] ?? null;
        }
        return null;
    }

    public function uapiCall($module, $function, $params = []) {
        $domain = Session::getDomain();
        $query = http_build_query($params);
        $url = "https://{$domain}:{$this->port}/execute/{$module}/{$function}?{$query}";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: cpanel {$this->username}:{$this->token}"]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true);
        if ($httpCode !== 200 || !isset($data['status']) || intval($data['status']) !== 1) {
            throw new Exception('cPanel API Error: ' . ($data['errors'][0] ?? 'Unknown error'));
        }
        return $data;
    }

    public function getDiskUsage() {
        return $this->uapiCall('Quota', 'get_quota_info');
    }

    public function getDomains() {
        return $this->uapiCall('DomainInfo', 'list_domains');
    }

    public function getEmails($page = 1, $perPage = 10) {
        return $this->uapiCall('Email', 'list_pops_with_disk', [
            'api.paginate' => 1,
            'api.paginate_page' => $page,
            'api.paginate_size' => $perPage
        ]);
    }

    public function createEmail($email, $password, $quota = 250) {
        return $this->uapiCall('Email', 'add_pop', [
            'email' => $email,
            'password' => $password,
            'quota' => $quota
        ]);
    }

    public function changePassword($email, $password) {
        return $this->uapiCall('Email', 'passwd_pop', [
            'email' => $email,
            'password' => $password
        ]);
    }

    public function deleteEmail($email) {
        return $this->uapiCall('Email', 'delete_pop', ['email' => $email]);
    }

    public function getSslCerts() {
        return $this->uapiCall('SSL', 'list_certs');
    }

    public function checkServerStatus() {
        $fp = @fsockopen($this->host, $this->port, $errno, $errstr, 5);
        if ($fp) {
            fclose($fp);
            return true;
        }
        return false;
    }
}
