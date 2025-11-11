<?php

class AdminController extends BaseController {
    private $userModel;
    private $quoteModel;
    private $permissionModel;
    private $cpanelService;

    public function __construct() {
        $this->userModel = new User();
        $this->quoteModel = new Quote();
        $this->permissionModel = new Permission();
        $this->cpanelService = new CpanelService();
    }

    public function users() {
        $this->requireSuperuser();

        $users = $this->userModel->all();
        $this->view('admin/users', ['users' => $users]);
    }

    public function createUser() {
        $this->requireSuperuser();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/users');
        }

        CSRF::check();

        $username = trim($_POST['cpanel_username'] ?? '');
        $domain = trim($_POST['cpanel_domain'] ?? '');
        $password = trim($_POST['cpanel_password'] ?? '');
        $profileName = trim($_POST['profile_name'] ?? '');
        $profilePicture = trim($_POST['profile_picture'] ?? '');
        $packageName = trim($_POST['package_name'] ?? '');
        $isSuperuser = isset($_POST['is_superuser']) ? 1 : 0;
        $userRole = trim($_POST['user_role'] ?? 'client');

        if (empty($username) || empty($domain) || empty($password)) {
            Session::set('error', 'Username, domain, and password are required');
            $this->redirect('/admin/users');
        }

        // Create cPanel API token
        $token = $this->cpanelService->createToken($username, $password);

        if (!$token) {
            Session::set('error', 'Failed to create cPanel API token');
            $this->redirect('/admin/users');
        }

        $data = [
            'cpanel_username' => $username,
            'cpanel_domain' => $domain,
            'cpanel_password' => $password,
            'cpanel_api_token' => $token,
            'profile_name' => $profileName,
            'profile_picture' => $profilePicture,
            'package_name' => $packageName,
            'is_superuser' => $isSuperuser,
            'user_role' => $userRole
        ];

        if ($this->userModel->create($data)) {
            Session::set('success', 'User created successfully');
            Session::set('new_user_username', $username);
            Session::set('new_user_password', $password);
            Session::set('new_user_domain', $domain);
        } else {
            Session::set('error', 'Failed to create user');
        }

        $this->redirect('/admin/users');
    }

    public function editUser() {
        $this->requireSuperuser();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/users');
        }

        CSRF::check();

        $id = (int)($_POST['id'] ?? 0);
        $username = trim($_POST['cpanel_username'] ?? '');
        $domain = trim($_POST['cpanel_domain'] ?? '');
        $password = trim($_POST['cpanel_password'] ?? '');
        $profileName = trim($_POST['profile_name'] ?? '');
        $profilePicture = trim($_POST['profile_picture'] ?? '');
        $packageName = trim($_POST['package_name'] ?? '');
        $isSuperuser = isset($_POST['is_superuser']) ? 1 : 0;
        $userRole = trim($_POST['user_role'] ?? 'client');

        $user = $this->userModel->find($id);
        if (!$user) {
            Session::set('error', 'User not found');
            $this->redirect('/admin/users');
        }

        $token = $user['cpanel_api_token'];
        if ($password !== $user['cpanel_password']) {
            $token = $this->cpanelService->createToken($username, $password);
            if (!$token) {
                Session::set('error', 'Failed to create cPanel API token');
                $this->redirect('/admin/users');
            }
        }

        $data = [
            'cpanel_username' => $username,
            'cpanel_domain' => $domain,
            'cpanel_password' => $password,
            'cpanel_api_token' => $token,
            'profile_name' => $profileName,
            'profile_picture' => $profilePicture,
            'package_name' => $packageName,
            'is_superuser' => $isSuperuser,
            'user_role' => $userRole
        ];

        if ($this->userModel->update($id, $data)) {
            Session::set('success', 'User updated successfully');
        } else {
            Session::set('error', 'Failed to update user');
        }

        $this->redirect('/admin/users');
    }

    public function deleteUser() {
        $this->requireSuperuser();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/users');
        }

        CSRF::check();

        $id = (int)($_POST['delete_user'] ?? 0);

        if ($this->userModel->delete($id)) {
            Session::set('success', 'User deleted successfully');
        } else {
            Session::set('error', 'Failed to delete user');
        }

        $this->redirect('/admin/users');
    }

    public function quotes() {
        $this->requireSuperuser();

        $quotes = $this->quoteModel->all();
        $quotesCount = $this->quoteModel->count();

        $this->view('admin/quotes', [
            'quotes' => $quotes,
            'quotesCount' => $quotesCount
        ]);
    }

    public function createQuote() {
        $this->requireSuperuser();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/quotes');
        }

        CSRF::check();

        $text = trim($_POST['quote_text'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $imageUrl = trim($_POST['image_url'] ?? '');

        if ($this->quoteModel->count() >= 20) {
            Session::set('error', 'Quote limit of 20 reached. Please delete an existing quote first.');
            $this->redirect('/admin/quotes');
        }

        if ($this->quoteModel->create($text, $author, $imageUrl)) {
            Session::set('success', 'Quote added successfully');
        } else {
            Session::set('error', 'Failed to add quote');
        }

        $this->redirect('/admin/quotes');
    }

    public function editQuote() {
        $this->requireSuperuser();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/quotes');
        }

        CSRF::check();

        $id = (int)($_POST['id'] ?? 0);
        $text = trim($_POST['quote_text'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $imageUrl = trim($_POST['image_url'] ?? '');

        if ($this->quoteModel->update($id, $text, $author, $imageUrl)) {
            Session::set('success', 'Quote updated successfully');
        } else {
            Session::set('error', 'Failed to update quote');
        }

        $this->redirect('/admin/quotes');
    }

    public function deleteQuote() {
        $this->requireSuperuser();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/quotes');
        }

        CSRF::check();

        $id = (int)($_POST['delete_quote'] ?? 0);

        if ($this->quoteModel->delete($id)) {
            Session::set('success', 'Quote deleted successfully');
        } else {
            Session::set('error', 'Failed to delete quote');
        }

        $this->redirect('/admin/quotes');
    }

    public function permissions() {
        $this->requireSuperuser();

        $permissions = $this->permissionModel->getAllPermissions();
        
        // Group by role
        $grouped = [];
        foreach ($permissions as $perm) {
            $grouped[$perm['role']][] = $perm;
        }

        $this->view('admin/permissions', ['permissions' => $grouped]);
    }

    public function updatePermissions() {
        $this->requireSuperuser();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/permissions');
        }

        CSRF::check();

        $permissions = $_POST['permissions'] ?? [];

        foreach ($permissions as $key => $value) {
            list($role, $menuItem) = explode('|', $key);
            $canAccess = $value == 1 ? 1 : 0;
            $this->permissionModel->update($role, $menuItem, $canAccess);
        }

        Session::set('success', 'Permissions updated successfully');
        $this->redirect('/admin/permissions');
    }
}
