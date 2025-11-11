<?php

class AuthController extends BaseController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function showLogin() {
        if (Session::isLoggedIn()) {
            $this->redirect('/dashboard');
        }
        $this->view('auth/login');
    }

    public function login() {
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
            Session::set('error', 'Invalid domain or password');
            $this->redirect('/');
        }

        $username = $user['cpanel_username'];
        $cpanelService = new CpanelService();
        $token = $cpanelService->createToken($username, $password);

        if (!$token) {
            Session::set('error', 'Invalid domain or password');
            $this->redirect('/');
        }

        // Set session variables
        Session::set('cpanel_username', $username);
        Session::set('cpanel_domain', $domain);
        Session::set('cpanel_api_token', $token);
        Session::set('is_superuser', $user['is_superuser']);
        Session::set('user_role', $user['user_role']);
        Session::set('profile_name', $user['profile_name']);
        Session::set('profile_picture', $user['profile_picture']);
        Session::set('package_name', $user['package_name']);

        // Load permissions
        $permissionModel = new Permission();
        Session::set('user_permissions', $permissionModel->getByRole($user['user_role']));

        $this->redirect('/dashboard');
    }

    public function logout() {
        Session::destroy();
        $this->redirect('/');
    }

    public function switchDomain() {
        $this->requireSuperuser();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/dashboard');
        }

        CSRF::check();

        $domain = trim($_POST['switch_domain'] ?? '');
        $user = $this->userModel->findByDomain($domain);

        if ($user) {
            Session::set('cpanel_username', $user['cpanel_username']);
            Session::set('cpanel_domain', $user['cpanel_domain']);
            Session::set('cpanel_api_token', $user['cpanel_api_token']);
            Session::set('profile_name', $user['profile_name']);
            Session::set('profile_picture', $user['profile_picture']);
            Session::set('package_name', $user['package_name']);
        }

        $this->redirect('/dashboard');
    }
}
