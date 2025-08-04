<?php
// models/AuditLog.php

class AuditLog {
    private $conn;
    private $table_name = "audit_logs";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Logs an action to the database.
     * @param string $action The action performed (e.g., 'login', 'update_employee').
     * @param string|null $details Additional details about the action.
     * @param int|null $user_id The ID of the user who performed the action.
     * @return bool Returns true if successful, false otherwise.
     */
    public function log($action, $details = null, $user_id = null) {
        try {
            $query = "INSERT INTO " . $this->table_name . " (user_id, action, details, ip_address, timestamp) 
                      VALUES (:user_id, :action, :details, :ip_address, NOW())";
            
            $stmt = $this->conn->prepare($query);

            // Get user ID from session if not provided
            $final_user_id = $user_id ?? ($_SESSION['user_id'] ?? null);
            
            // Get IP Address
            $ip_address = $this->getClientIpAddress();

            // Bind data
            $stmt->bindParam(':user_id', $final_user_id, PDO::PARAM_INT);
            $stmt->bindParam(':action', $action, PDO::PARAM_STR);
            $stmt->bindParam(':details', $details, PDO::PARAM_STR);
            $stmt->bindParam(':ip_address', $ip_address, PDO::PARAM_STR);

            return $stmt->execute();
            
        } catch (Exception $e) {
            error_log("AuditLog error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the real client IP address
     * @return string
     */
    private function getClientIpAddress() {
        // Check for various headers that might contain the real IP
        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {
            if (isset($_SERVER[$header]) && !empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                // Handle comma-separated IPs (like X-Forwarded-For)
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    }

    /**
     * อ่านข้อมูล Log ทั้งหมด
     * @param int $limit จำนวนรายการที่จะแสดง
     * @param int $offset เริ่มจากรายการที่
     * @return PDOStatement
     */
    public function read($limit = 100, $offset = 0) {
        try {
            $query = "SELECT a.*, e.first_name_th, e.last_name_th, e.employee_code
                      FROM " . $this->table_name . " a
                      LEFT JOIN employees e ON a.user_id = e.id
                      ORDER BY a.timestamp DESC
                      LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt;
            
        } catch (Exception $e) {
            error_log("AuditLog read error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * นับจำนวน Log ทั้งหมด
     * @return int
     */
    public function count() {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
            
        } catch (Exception $e) {
            error_log("AuditLog count error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * ค้นหา Log ตาม user_id
     * @param int $user_id
     * @param int $limit
     * @return PDOStatement|false
     */
    public function readByUserId($user_id, $limit = 50) {
        try {
            $query = "SELECT a.*, e.first_name_th, e.last_name_th, e.employee_code
                      FROM " . $this->table_name . " a
                      LEFT JOIN employees e ON a.user_id = e.id
                      WHERE a.user_id = :user_id
                      ORDER BY a.timestamp DESC
                      LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt;
            
        } catch (Exception $e) {
            error_log("AuditLog readByUserId error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ค้นหา Log ตาม action
     * @param string $action
     * @param int $limit
     * @return PDOStatement|false
     */
    public function readByAction($action, $limit = 50) {
        try {
            $query = "SELECT a.*, e.first_name_th, e.last_name_th, e.employee_code
                      FROM " . $this->table_name . " a
                      LEFT JOIN employees e ON a.user_id = e.id
                      WHERE a.action = :action
                      ORDER BY a.timestamp DESC
                      LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':action', $action, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt;
            
        } catch (Exception $e) {
            error_log("AuditLog readByAction error: " . $e->getMessage());
            return false;
        }
    }
}
?>