<?php

class AuthController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function showLogin()
    {
        if (Session::isLoggedIn()) {
            $this->redirect('/dashboard');
        }
        $this->view('auth/login');
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
        }

        CSRF::check();

        $domain = trim($_POST['domain'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($domain) || empty($password)) {
            Session::set('error', 'Domain and password are required');
            $this->redirect('/');
        }

        $user = $this->userModel->findByDomain($domain);

        if (!$user) {
            Session::set('error', 'Domain not found in system.');
            $this->redirect('/');
        }

        $username = $user['cpanel_username'];
        $userRole = $user['user_role'] ?? 'client';

        // Try DB token first (legacy logic)
        if (!empty($user['api_token'])) {
            try {
                $ch = curl_init("https://{$domain}:2083/execute/Email/list_pops");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: cpanel {$username}:{$user['api_token']}"]);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                $resp = curl_exec($ch);
                curl_close($ch);
                $json = json_decode($resp, true);
                if (isset($json['status']) && intval($json['status']) === 1) {
                    $apiToken = $user['api_token'];
                } else {
                    $apiToken = null;
                }
            } catch (Exception $e) {
                $apiToken = null;
            }
        } else {
            $apiToken = null;
        }

        // Create fresh token with password (legacy logic)
        if (!$apiToken) {
            $url = "https://server.driftnimbus.com:2083/execute/Tokens/create_token";
            $query = http_build_query(['label' => 'DriftNimbusDashboard']);
            $ch = curl_init("{$url}?{$query}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, "{$username}:{$password}");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $response = curl_exec($ch);
            curl_close($ch);
            $apiTokenData = json_decode($response, true);
            if (isset($apiTokenData['status']) && intval($apiTokenData['status']) === 1) {
                $apiToken = $apiTokenData['data'][0]['token'] ?? null;
                if ($apiToken) {
                    // Update DB
                    $this->userModel->update($user['id'], [
                        'cpanel_username' => $username,
                        'domain' => $domain,
                        'cpanel_password' => $password,
                        'api_token' => $apiToken,
                        'full_name' => $user['full_name'],
                        'profile_picture_url' => $user['profile_picture_url'],
                        'package' => $user['package'],
                        'is_superuser' => $user['is_superuser'],
                        'user_role' => $userRole
                    ]);
                }
            } else {
                $apiToken = null;
            }
        }

        if (!$apiToken) {
            Session::set('error', 'Login failed.');
            $this->redirect('/');
        }

        // Set session and redirect
        Session::set('cpanel_username', $username);
        Session::set('cpanel_domain', $domain);
        Session::set('cpanel_api_token', $apiToken);
        Session::set('is_superuser', (int) ($user['is_superuser'] ?? 0));
        Session::set('user_role', $userRole);
        Session::set('profile_name', $user['full_name']);
        Session::set('profile_picture', $user['profile_picture_url']);
        Session::set('package_name', $user['package']);
        $permissionModel = new Permission();
        Session::set('user_permissions', $permissionModel->getByRole($userRole));

        addNotification('info', "Successful login to dashboard from node: {$domain}");

        $this->redirect('/dashboard');
    }

    public function logout()
    {
        addNotification('info', "User logged off from session.");
        Session::destroy();
        $this->redirect('/');
    }

    public function switchDomain()
    {
        $this->requireSuperuser();

        $userId = (int) ($_GET['id'] ?? 0);
        $user = $this->userModel->find($userId);

        if ($user) {
            $isSuperuser = Session::get('is_superuser');
            $userRole = Session::get('user_role');

            // Temporarily set session to test token
            Session::set('cpanel_username', $user['cpanel_username']);
            Session::set('cpanel_domain', $user['domain']);
            Session::set('cpanel_api_token', $user['api_token']);

            // Test if token is valid
            $cpanelService = new CpanelService();
            try {
                $cpanelService->getDiskUsage();
            } catch (Exception $e) {
                // Token invalid, regenerate using password
                if (!empty($user['cpanel_password'])) {
                    $newToken = $cpanelService->createToken($user['cpanel_username'], $user['cpanel_password']);
                    if ($newToken) {
                        $this->userModel->update($userId, [
                            'cpanel_username' => $user['cpanel_username'],
                            'domain' => $user['domain'],
                            'cpanel_password' => $user['cpanel_password'],
                            'api_token' => $newToken,
                            'full_name' => $user['full_name'],
                            'profile_picture_url' => $user['profile_picture_url'],
                            'package' => $user['package'],
                            'is_superuser' => $user['is_superuser'],
                            'user_role' => $user['user_role']
                        ]);
                        Session::set('cpanel_api_token', $newToken);
                    }
                }
            }

            Session::set('profile_name', $user['full_name']);
            Session::set('profile_picture', $user['profile_picture_url']);
            Session::set('package_name', $user['package']);
            Session::set('is_superuser', $isSuperuser);
            Session::set('user_role', $userRole);
        }

        $this->redirect('/dashboard');
    }
}
