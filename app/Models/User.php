<?php

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function findByDomain($domain)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE domain = ?");
        $stmt->bind_param("s", $domain);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function all()
    {
        $result = $this->db->query("SELECT * FROM users ORDER BY id DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO users (cpanel_username, domain, api_token, full_name, email, profile_picture_url, package, is_superuser, user_role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "sssssssss",
            $data['cpanel_username'],
            $data['domain'],
            $data['api_token'],
            $data['full_name'],
            $data['email'],
            $data['profile_picture_url'],
            $data['package'],
            $data['is_superuser'],
            $data['user_role']
        );
        return $stmt->execute();
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE users SET cpanel_username = ?, domain = ?, api_token = ?, full_name = ?, email = ?, profile_picture_url = ?, package = ?, is_superuser = ?, user_role = ? WHERE id = ?");
        $stmt->bind_param(
            "sssssssssi",
            $data['cpanel_username'],
            $data['domain'],
            $data['api_token'],
            $data['full_name'],
            $data['email'],
            $data['profile_picture_url'],
            $data['package'],
            $data['is_superuser'],
            $data['user_role'],
            $id
        );
        return $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function authenticate($domain, $password)
    {
        // Password authentication is deprecated as column is removed.
        // Logic should be handled via API Token verification or external auth.
        return null;
    }

    public function updateProfile($id, $name, $picture)
    {
        $stmt = $this->db->prepare("UPDATE users SET full_name = ?, profile_picture_url = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $picture, $id);
        return $stmt->execute();
    }
}
