<?php

class Role {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function all() {
        $result = $this->db->query("SELECT * FROM roles ORDER BY id");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function find($role) {
        $stmt = $this->db->prepare("SELECT * FROM roles WHERE role = ?");
        $stmt->bind_param("s", $role);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
