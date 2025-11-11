<?php

class SettingsController extends BaseController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function index() {
        $this->requireAuth();

        $domain = Session::getDomain();
        $user = $this->userModel->findByDomain($domain);

        $this->view('settings/index', [
            'user' => $user,
            'profileName' => Session::get('profile_name'),
            'profilePicture' => Session::get('profile_picture')
        ]);
    }

    public function update() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/settings');
        }

        CSRF::check();

        $domain = Session::getDomain();
        $user = $this->userModel->findByDomain($domain);

        if (!$user) {
            Session::set('error', 'User not found');
            $this->redirect('/settings');
        }

        $profileName = trim($_POST['profile_name'] ?? '');
        $profilePicture = trim($_POST['profile_picture'] ?? '');

        if (empty($profileName)) {
            Session::set('error', 'Profile name is required');
            $this->redirect('/settings');
        }

        if ($this->userModel->updateProfile($user['id'], $profileName, $profilePicture)) {
            Session::set('profile_name', $profileName);
            Session::set('profile_picture', $profilePicture);
            Session::set('success', 'Profile updated successfully');
        } else {
            Session::set('error', 'Failed to update profile');
        }

        $this->redirect('/settings');
    }
}
