<?php
// models/Setting.php

class Setting {
    private $conn;
    private $table_name = "system_settings";

    public function __construct($db) {
        $this->conn = $db;
    }

    // ดึงข้อมูลการตั้งค่าทั้งหมด
    public function getAllSettings() {
        $settings = [];
        $query = "SELECT setting_key, setting_value FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    // ===== ฟังก์ชันใหม่: ดึงค่าการตั้งค่ารายการเดียว =====
    public function getSettingValue($key, $default = null) {
        $query = "SELECT setting_value FROM " . $this->table_name . " WHERE setting_key = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $key);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['setting_value'];
        }
        return $default; // คืนค่า default หากไม่พบ key
    }

    // บันทึกการตั้งค่า
    public function saveSettings($settings_array) {
        $query = "INSERT INTO " . $this->table_name . " (setting_key, setting_value) VALUES (:key, :value)
                  ON DUPLICATE KEY UPDATE setting_value = :value";
        
        $stmt = $this->conn->prepare($query);

        try {
            foreach ($settings_array as $key => $value) {
                $stmt->bindParam(':key', $key);
                $stmt->bindParam(':value', $value);
                $stmt->execute();
            }
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
