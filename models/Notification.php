<?php
// models/Notification.php

class Notification {
    private $conn;
    private $table_name = "notifications";

    public $id;
    public $user_id;
    public $message;
    public $link;
    public $is_read;

    public function __construct($db) {
        $this->conn = $db;
    }

    // สร้างการแจ้งเตือนใหม่
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET user_id = :user_id, message = :message, link = :link";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':message', $this->message);
        $stmt->bindParam(':link', $this->link);

        return $stmt->execute();
    }

    // ดึงการแจ้งเตือนที่ยังไม่อ่านของผู้ใช้
    public static function getUnreadByUser($db, $user_id) {
        $query = "SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC LIMIT 5";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt;
    }

    // อัปเดตสถานะการแจ้งเตือนเป็น "อ่านแล้ว"
    public static function markAsRead($db, $user_id) {
        $query = "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $user_id);
        return $stmt->execute();
    }
}
?>
