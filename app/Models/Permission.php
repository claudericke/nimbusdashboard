<?php

class Permission
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getByRole($role)
    {
        $stmt = $this->db->prepare("SELECT menu_item FROM permissions WHERE role_name = ? AND can_access = 1");
        $stmt->bind_param("s", $role);
        $stmt->execute();
        $result = $stmt->get_result();

        $permissions = [];
        while ($row = $result->fetch_assoc()) {
            $permissions[] = $row['menu_item'];
        }
        return $permissions;
    }

    public function canAccess($menuItem, $role)
    {
        $stmt = $this->db->prepare("SELECT can_access FROM permissions WHERE role_name = ? AND menu_item = ?");
        $stmt->bind_param("ss", $role, $menuItem);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? (bool) $row['can_access'] : false;
    }

    public function update($role, $menuItem, $canAccess)
    {
        $sql = "INSERT INTO permissions (role_name, menu_item, can_access) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE can_access = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->db->getConnection()->error);
            return false;
        }
        $canAccess2 = $canAccess;
        $stmt->bind_param("ssii", $role, $menuItem, $canAccess, $canAccess2);
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            return false;
        }
        return true;
    }

    public function getAllPermissions()
    {
        $result = $this->db->query("SELECT * FROM permissions ORDER BY role_name, menu_item");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
