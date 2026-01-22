<?php

class MigrationService
{
    private $db;
    private $migrationsDir;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->migrationsDir = __DIR__ . '/../../migrations';
        $this->init();
    }

    private function init()
    {
        // Ensure migrations_log table exists
        $sql = "CREATE TABLE IF NOT EXISTS migrations_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            filename VARCHAR(255) NOT NULL UNIQUE,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->query($sql);
    }

    public function getMigrations()
    {
        $files = glob($this->migrationsDir . '/*.sql');
        $migrations = [];

        // Get executed migrations
        $result = $this->db->query("SELECT filename, executed_at FROM migrations_log");
        $executed = [];
        while ($row = $result->fetch_assoc()) {
            $executed[$row['filename']] = $row['executed_at'];
        }

        foreach ($files as $file) {
            $filename = basename($file);
            $migrations[] = [
                'filename' => $filename,
                'status' => isset($executed[$filename]) ? 'completed' : 'pending',
                'executed_at' => $executed[$filename] ?? null
            ];
        }

        // Sort: Pending first, then by name
        usort($migrations, function ($a, $b) {
            if ($a['status'] === $b['status']) {
                return strcmp($a['filename'], $b['filename']);
            }
            return $a['status'] === 'pending' ? -1 : 1;
        });

        return $migrations;
    }

    public function runMigration($filename)
    {
        $path = $this->migrationsDir . '/' . $filename;
        if (!file_exists($path)) {
            return ['success' => false, 'message' => 'File not found'];
        }

        // Check if already executed
        $stmt = $this->db->prepare("SELECT id FROM migrations_log WHERE filename = ?");
        $stmt->bind_param("s", $filename);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            return ['success' => false, 'message' => 'Migration already executed'];
        }

        $sql = file_get_contents($path);

        // Execute multiple statements
        $mysqli = $this->db->getConnection();
        if ($mysqli->multi_query($sql)) {
            // Consume all results to clear the connection
            do {
                if ($result = $mysqli->store_result()) {
                    $result->free();
                }
            } while ($mysqli->more_results() && $mysqli->next_result());

            // Log execution
            $stmt = $this->db->prepare("INSERT INTO migrations_log (filename) VALUES (?)");
            $stmt->bind_param("s", $filename);
            $stmt->execute();

            return ['success' => true, 'message' => 'Migration executed successfully'];
        } else {
            return ['success' => false, 'message' => 'Database error: ' . $this->db->error];
        }
    }
}
