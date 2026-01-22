<?php

class AdminController extends BaseController
{
    private $userModel;
    private $quoteModel;
    private $permissionModel;
    private $cpanelService;
    private $activityLog;
    private $migrationService;

    public function __construct()
    {
        $this->userModel = new User();
        $this->quoteModel = new Quote();
        $this->permissionModel = new Permission();
        $this->cpanelService = new CpanelService();
        $this->activityLog = new ActivityLog();
        $this->migrationService = new MigrationService();
    }

    public function users()
    {
        $this->requireSuperuser();

        $users = $this->userModel->all();
        $this->view('admin/users', ['users' => $users]);
    }

    public function createUser()
    {
        $this->requireSuperuser();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/users');
        }

        CSRF::check();

        $username = trim($_POST['cpanel_username'] ?? '');
        $domain = trim($_POST['domain'] ?? '');
        $password = ''; // No longer used
        $email = trim($_POST['email'] ?? '');
        $profileName = trim($_POST['full_name'] ?? '');
        $profilePicture = trim($_POST['profile_picture'] ?? '');
        $packageName = trim($_POST['package'] ?? 'Solopreneur');
        $isSuperuser = isset($_POST['is_superuser']) ? 1 : 0;
        $userRole = trim($_POST['user_role'] ?? 'client');

        $token = trim($_POST['api_token'] ?? '');

        if (empty($username) || empty($domain) || empty($token)) {
            Session::set('error', 'Username, domain, and API Token are required');
            $this->redirect('/admin/users');
        }

        // $token is already set from POST

        $data = [
            'cpanel_username' => $username,
            'domain' => $domain,
            // 'cpanel_password' => $password, // Removed
            'email' => $email,
            'api_token' => $token,
            'full_name' => $profileName,
            'profile_picture_url' => $profilePicture,
            'package' => $packageName,
            'is_superuser' => $isSuperuser,
            'user_role' => $userRole
        ];

        try {
            if ($this->userModel->create($data)) {
                // Send Onboarding Email
                if (!empty($email)) {
                    $this->sendOnboardingEmail($email, $username, $password, $domain);
                }
                Session::set('success', 'User created successfully: ' . h($username));

                // Log activity
                $this->activityLog->log($this->getCurrentUserId(), 'user', "Created new user: $username");
            } else {
                Session::set('error', 'Database Error: Failed to save user record. Username or Domain might already exist.');
            }
        } catch (Exception $e) {
            Session::set('error', 'System Error: ' . $e->getMessage());
        }

        $this->redirect('/admin/users');
    }

    private function sendOnboardingEmail($to, $username, $password, $domain)
    {
        $template = file_get_contents(__DIR__ . '/../../onboardingEmail.md');
        $body = str_replace(
            ['{{username}}', '{{password}}', '{{domainURI}}'],
            [$username, $password, $domain],
            $template
        );

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = env('SMTP_HOST', 'localhost');
            $mail->SMTPAuth = true;
            $mail->Username = env('SMTP_USER');
            $mail->Password = env('SMTP_PASS');
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = env('SMTP_PORT', 587);

            // Recipients
            $mail->setFrom(env('SMTP_FROM', 'support@driftnimbus.com'), 'Drift Nimbus Support');
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Welcome to Drift Nimbus - Your digital backbone is live.';
            $mail->Body = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Log error or set session error
            return false;
        }
    }

    public function editUser()
    {
        $this->requireSuperuser();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/users');
        }

        CSRF::check();

        $id = (int) ($_POST['id'] ?? 0);
        $username = trim($_POST['cpanel_username'] ?? '');
        $domain = trim($_POST['cpanel_domain'] ?? '');
        // $password = trim($_POST['cpanel_password'] ?? ''); // Removed
        $email = trim($_POST['email'] ?? '');
        $profileName = trim($_POST['full_name'] ?? '');
        $profilePicture = trim($_POST['profile_picture'] ?? '');
        $packageName = trim($_POST['package'] ?? '');
        $isSuperuser = isset($_POST['is_superuser']) ? 1 : 0;
        $userRole = trim($_POST['user_role'] ?? 'client');
        $token = trim($_POST['api_token'] ?? '');

        $user = $this->userModel->find($id);
        if (!$user) {
            Session::set('error', 'User not found');
            $this->redirect('/admin/users');
        }

        // If token explicitly provided in edit, use it. Otherwise keep existing.
        if (empty($token)) {
            $token = $user['api_token'];
        }

        $data = [
            'cpanel_username' => $username,
            'domain' => $domain,
            // 'cpanel_password' => $user['cpanel_password'], // Removed
            'email' => $email,
            'api_token' => $token,
            'full_name' => $profileName,
            'profile_picture_url' => $profilePicture,
            'package' => $packageName,
            'is_superuser' => $isSuperuser,
            'user_role' => $userRole
        ];

        if ($this->userModel->update($id, $data)) {
            Session::set('success', 'User updated successfully');
            // Log activity
            $this->activityLog->log($this->getCurrentUserId(), 'user', "Updated user: $username");
        } else {
            Session::set('error', 'Failed to update user');
        }

        $this->redirect('/admin/users');
    }

    public function deleteUser()
    {
        $this->requireSuperuser();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/users');
        }

        CSRF::check();

        $id = (int) ($_POST['delete_user'] ?? 0);

        if ($this->userModel->delete($id)) {
            Session::set('success', 'User deleted successfully');
            // Log activity
            $this->activityLog->log($this->getCurrentUserId(), 'user', "Deleted user ID: $id");
        } else {
            Session::set('error', 'Failed to delete user');
        }

        $this->redirect('/admin/users');
    }

    public function quotes()
    {
        $this->requireSuperuser();

        $quotes = $this->quoteModel->all();
        $quotesCount = $this->quoteModel->count();

        $this->view('admin/quotes', [
            'quotes' => $quotes,
            'quotesCount' => $quotesCount
        ]);
    }

    public function createQuote()
    {
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

    public function editQuote()
    {
        $this->requireSuperuser();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/quotes');
        }

        CSRF::check();

        $id = (int) ($_POST['id'] ?? 0);
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

    public function deleteQuote()
    {
        $this->requireSuperuser();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/quotes');
        }

        CSRF::check();

        $id = (int) ($_POST['delete_quote'] ?? 0);

        if ($this->quoteModel->delete($id)) {
            Session::set('success', 'Quote deleted successfully');
        } else {
            Session::set('error', 'Failed to delete quote');
        }

        $this->redirect('/admin/quotes');
    }

    public function permissions()
    {
        $this->requireSuperuser();

        $permissions = $this->permissionModel->getAllPermissions();

        // Group by role
        $grouped = [];
        foreach ($permissions as $perm) {
            $grouped[$perm['role_name']][] = $perm;
        }

        $this->view('admin/permissions', ['permissions' => $grouped]);
    }

    public function updatePermissions()
    {
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

    public function migrations()
    {
        $this->requireSuperuser();

        $migrations = $this->migrationService->getMigrations();

        $this->view('admin/migrations', ['migrations' => $migrations]);
    }

    public function runMigration()
    {
        $this->requireSuperuser();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/migrations');
        }

        CSRF::check();

        $filename = $_POST['filename'] ?? '';

        $result = $this->migrationService->runMigration($filename);

        if ($result['success']) {
            Session::set('success', $result['message']);
            // Log activity
            $this->activityLog->log($this->getCurrentUserId(), 'system', "Ran migration: $filename");
        } else {
            Session::set('error', $result['message']);
        }

        $this->redirect('/admin/migrations');
    }
}
