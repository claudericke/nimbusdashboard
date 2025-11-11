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
        $url = "https://{$this->host}:{$this->port}/login/?login_only=1";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_exec($ch);
        curl_close($ch);

        $url = "https://{$this->host}:{$this->port}/execute/Tokens/create_full_access";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['name' => 'dashboard_token']));
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['data']['token'] ?? null;
    }

    public function uapiCall($module, $function, $params = []) {
        $query = http_build_query($params);
        $url = "https://{$this->host}:{$this->port}/execute/{$module}/{$function}?{$query}";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: cpanel {$this->username}:{$this->token}"]);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
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
