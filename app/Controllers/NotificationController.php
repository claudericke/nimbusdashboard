<?php

class NotificationController extends BaseController
{
    private $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new Notification();
    }

    /**
     * Fetch latest notifications for the current user
     */
    public function latest()
    {
        $this->requireAuth();

        $userId = Session::get('user_id');
        $notifications = $this->notificationModel->getLatest($userId);
        $unreadCount = $this->notificationModel->getUnreadCount($userId);

        $this->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markRead()
    {
        $this->requireAuth();
        CSRF::check();

        $userId = Session::get('user_id');
        if ($this->notificationModel->markAllAsRead($userId)) {
            $this->json(['success' => true]);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to mark notifications as read'], 500);
        }
    }
}
