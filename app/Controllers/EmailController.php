<?php

class EmailController extends BaseController {
    private $cpanelService;

    public function __construct() {
        $this->cpanelService = new CpanelService();
    }

    public function index() {
        $this->requireAuth();

        $page = $_GET['page'] ?? 1;
        $emailsData = $this->cpanelService->getEmails($page, 10);
        
        $this->view('emails/index', [
            'emails' => $emailsData['data'] ?? [],
            'metadata' => $emailsData['metadata'] ?? [],
            'currentPage' => $page
        ]);
    }

    public function create() {
        $this->requireAuth();
        $this->view('emails/create');
    }

    public function store() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/emails');
        }

        CSRF::check();

        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $quota = (int)($_POST['quota'] ?? 250);

        if (empty($email) || empty($password)) {
            Session::set('error', 'Email and password are required');
            $this->redirect('/emails/create');
        }

        $result = $this->cpanelService->createEmail($email, $password, $quota);

        if ($result['status'] == 1) {
            Session::set('success', 'Email account created successfully');
            Session::set('new_email', $email);
            Session::set('new_password', $password);
        } else {
            Session::set('error', $result['errors'][0] ?? 'Failed to create email account');
        }

        $this->redirect('/emails');
    }

    public function changePassword() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/emails');
        }

        CSRF::check();

        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['new_password'] ?? '');

        if (empty($email) || empty($password)) {
            Session::set('error', 'Email and password are required');
            $this->redirect('/emails');
        }

        $result = $this->cpanelService->changePassword($email, $password);

        if ($result['status'] == 1) {
            Session::set('success', 'Password changed successfully');
            Session::set('changed_email', $email);
            Session::set('changed_password', $password);
        } else {
            Session::set('error', $result['errors'][0] ?? 'Failed to change password');
        }

        $this->redirect('/emails');
    }

    public function delete() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/emails');
        }

        CSRF::check();

        $email = trim($_POST['delete_email'] ?? '');

        if (empty($email)) {
            Session::set('error', 'Email is required');
            $this->redirect('/emails');
        }

        $result = $this->cpanelService->deleteEmail($email);

        if ($result['status'] == 1) {
            Session::set('success', 'Email account deleted successfully');
        } else {
            Session::set('error', $result['errors'][0] ?? 'Failed to delete email account');
        }

        $this->redirect('/emails');
    }
}
