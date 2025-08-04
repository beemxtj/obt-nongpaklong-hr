<?php
// models/Role.php

class Role {
    private $conn;
    private $table_name = "roles";

    public $id;
    public $role_name;
    public $permissions;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET role_name = :role_name, permissions = :permissions";
        $stmt = $this->conn->prepare($query);
        $this->role_name = htmlspecialchars(strip_tags($this->role_name));
        $stmt->bindParam(':role_name', $this->role_name);
        $stmt->bindParam(':permissions', $this->permissions);
        return $stmt->execute();
    }

    // ===== ฟังก์ชันใหม่: อ่านข้อมูลรายการเดียว =====
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->role_name = $row['role_name'];
            $this->permissions = $row['permissions'];
        }
    }

    // ===== ฟังก์ชันใหม่: อัปเดตข้อมูล =====
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET role_name = :role_name, permissions = :permissions WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->role_name = htmlspecialchars(strip_tags($this->role_name));
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':role_name', $this->role_name);
        $stmt->bindParam(':permissions', $this->permissions);
        return $stmt->execute();
    }

    // ===== ฟังก์ชันใหม่: ลบข้อมูล =====
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }
}
?>
