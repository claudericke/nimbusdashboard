<?php

class ActivityLog
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function log($userId, $actionType, $description)
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        try {
            $stmt = $this->db->prepare("INSERT INTO activity_logs (user_id, action_type, description, ip_address) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $userId, $actionType, $description, $ip);
            return $stmt->execute();
        } catch (Exception $e) {
            // Silently fail if table doesn't exist to prevent app crash during setup/migrations
            return false;
        }
    }

    public function getRecent($limit = 10)
    {
        $stmt = $this->db->prepare("SELECT l.*, u.full_name, u.profile_picture_url FROM activity_logs l LEFT JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
