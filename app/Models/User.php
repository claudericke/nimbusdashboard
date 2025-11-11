<?php

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function findByDomain($domain) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE cpanel_domain = ?");
        $stmt->bind_param("s", $domain);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function all() {
        $result = $this->db->query("SELECT * FROM users ORDER BY id DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO users (cpanel_username, cpanel_domain, cpanel_password, cpanel_api_token, profile_name, profile_picture, package_name, is_superuser, user_role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", 
            $data['cpanel_username'],
            $data['cpanel_domain'],
            $data['cpanel_password'],
            $data['cpanel_api_token'],
            $data['profile_name'],
            $data['profile_picture'],
            $data['package_name'],
            $data['is_superuser'],
            $data['user_role']
        );
        return $stmt->execute();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE users SET cpanel_username = ?, cpanel_domain = ?, cpanel_password = ?, cpanel_api_token = ?, profile_name = ?, profile_picture = ?, package_name = ?, is_superuser = ?, user_role = ? WHERE id = ?");
        $stmt->bind_param("sssssssssi",
            $data['cpanel_username'],
            $data['cpanel_domain'],
            $data['cpanel_password'],
            $data['cpanel_api_token'],
            $data['profile_name'],
            $data['profile_picture'],
            $data['package_name'],
            $data['is_superuser'],
            $data['user_role'],
            $id
        );
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function authenticate($domain, $password) {
        $user = $this->findByDomain($domain);
        if ($user && $user['cpanel_password'] === $password) {
            return $user;
        }
        return null;
    }

    public function updateProfile($id, $name, $picture) {
        $stmt = $this->db->prepare("UPDATE users SET profile_name = ?, profile_picture = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $picture, $id);
        return $stmt->execute();
    }
}
