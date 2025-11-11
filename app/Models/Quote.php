<?php

class Quote {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function all() {
        $result = $this->db->query("SELECT * FROM quotes ORDER BY id DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function random() {
        $result = $this->db->query("SELECT * FROM quotes ORDER BY RAND() LIMIT 1");
        return $result->fetch_assoc();
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM quotes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function count() {
        $result = $this->db->query("SELECT COUNT(*) as count FROM quotes");
        $row = $result->fetch_assoc();
        return $row['count'];
    }

    public function create($text, $author, $imageUrl) {
        $stmt = $this->db->prepare("INSERT INTO quotes (quote_text, author, image_url) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $text, $author, $imageUrl);
        return $stmt->execute();
    }

    public function update($id, $text, $author, $imageUrl) {
        $stmt = $this->db->prepare("UPDATE quotes SET quote_text = ?, author = ?, image_url = ? WHERE id = ?");
        $stmt->bind_param("sssi", $text, $author, $imageUrl, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM quotes WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
