<?php

class SettingsController extends BaseController
{
    private $userModel;
    private $activityLog;

    public function __construct()
    {
        $this->userModel = new User();
        $this->activityLog = new ActivityLog();
    }

    public function index()
    {
        $this->requireAuth();

        $domain = Session::getDomain();
        $user = $this->userModel->findByDomain($domain);

        $this->view('settings/index', [
            'user' => $user,
            'profileName' => Session::get('profile_name'),
            'profilePicture' => Session::get('profile_picture')
        ]);
    }

    public function update()
    {
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

            // Log activity
            $this->activityLog->log($this->getCurrentUserId(), 'settings', "Updated profile settings");
        } else {
            Session::set('error', 'Failed to update profile');
        }

        $this->redirect('/settings');
    }

    public function uploadAvatar()
    {
        header('Content-Type: application/json');
        $this->requireAuth();
        CSRF::check();

        $input = json_decode(file_get_contents('php://input'), true);
        $imageData = $input['image'] ?? null;

        if (!$imageData) {
            echo json_encode(['success' => false, 'error' => 'No image data received']);
            return;
        }

        // Validate base64 format and extract data
        if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
            $type = strtolower($type[1]); // png, jpg, etc

            if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                echo json_encode(['success' => false, 'error' => 'Invalid image type']);
                return;
            }

            $imageData = base64_decode($imageData);

            if ($imageData === false) {
                echo json_encode(['success' => false, 'error' => 'Base64 decode failed']);
                return;
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid image data format']);
            return;
        }

        $userId = Session::get('user_id');
        $filename = 'avatar_' . $userId . '_' . time() . '.png';
        $path = __DIR__ . '/../../public/uploads/avatars/' . $filename;

        // Ensure directory exists
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        if (file_put_contents($path, $imageData)) {
            $url = '/public/uploads/avatars/' . $filename;

            // Update database
            $user = $this->userModel->find($userId);
            if ($this->userModel->updateProfile($userId, $user['full_name'], $url)) {
                Session::set('profile_picture', $url);
                echo json_encode(['success' => true, 'url' => $url]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to update database']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to save file']);
        }
    }
}
