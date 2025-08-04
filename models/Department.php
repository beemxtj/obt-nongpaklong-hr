<?php
// models/Department.php

class Department
{
    private $conn;
    private $table_name = "departments"; // *** ตรวจสอบชื่อตารางในฐานข้อมูลของคุณอีกครั้งว่าคือ "departments" จริงๆ ***

    public $id;
    public $name_th;
    public $name_en;
    public $description;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * อ่านข้อมูลแผนกทั้งหมด
     * @return PDOStatement|null คืนค่า PDOStatement ถ้าสำเร็จ, null ถ้าเกิดข้อผิดพลาด
     */
    public function readAll()
    {
        try {
            $query = "SELECT id, name_th, name_en, description FROM " . $this->table_name . " ORDER BY name_th ASC";
            $stmt = $this->conn->prepare($query);
            
            // ถ้า prepare() ล้มเหลว (ซึ่งไม่น่าจะเกิดขึ้นถ้า PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION ถูกตั้งค่า)
            if ($stmt === false) {
                error_log("Failed to prepare statement for Department->readAll(): " . implode(":", $this->conn->errorInfo()));
                return null; 
            }

            $stmt->execute();
            return $stmt; // คืนค่า PDOStatement
        } catch (PDOException $e) {
            // ดักจับและบันทึกข้อผิดพลาดจากฐานข้อมูล
            error_log("Database error in Department->readAll(): " . $e->getMessage());
            return null; // คืนค่า null เพื่อแจ้ง Controller ว่ามีปัญหา
        }
    }

    /**
     * อ่านข้อมูลแผนกเดียว
     * @param int $id ID ของแผนก
     * @return Department|false คืนค่า object ของ Department ถ้าพบ, false ถ้าไม่พบ
     */
    public function readOne()
    {
        try {
            $query = "SELECT id, name_th, name_en, description FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $this->name_th = $row['name_th'];
                $this->name_en = $row['name_en'];
                $this->description = $row['description'];
                return $this; 
            }
            return false; 
        } catch (PDOException $e) {
            error_log("Database error in Department->readOne(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * สร้างแผนกใหม่
     * @return bool true ถ้าสำเร็จ, false ถ้าล้มเหลว
     */
    public function create()
    {
        try {
            $query = "INSERT INTO " . $this->table_name . " SET name_th=:name_th, name_en=:name_en, description=:description";
            $stmt = $this->conn->prepare($query);

            $this->name_th = htmlspecialchars(strip_tags($this->name_th));
            $this->name_en = htmlspecialchars(strip_tags($this->name_en));
            $this->description = htmlspecialchars(strip_tags($this->description));

            $stmt->bindParam(":name_th", $this->name_th);
            $stmt->bindParam(":name_en", $this->name_en);
            $stmt->bindParam(":description", $this->description);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Database error in Department->create(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * อัปเดตแผนก
     * @return bool true ถ้าสำเร็จ, false ถ้าล้มเหลว
     */
    public function update()
    {
        try {
            $query = "UPDATE " . $this->table_name . " SET name_th=:name_th, name_en=:name_en, description=:description WHERE id=:id";
            $stmt = $this->conn->prepare($query);

            $this->name_th = htmlspecialchars(strip_tags($this->name_th));
            $this->name_en = htmlspecialchars(strip_tags($this->name_en));
            $this->description = htmlspecialchars(strip_tags($this->description));
            $this->id = htmlspecialchars(strip_tags($this->id));

            $stmt->bindParam(':name_th', $this->name_th);
            $stmt->bindParam(':name_en', $this->name_en);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':id', $this->id);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Database error in Department->update(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * ลบแผนก
     * @return bool true ถ้าสำเร็จ, false ถ้าล้มเหลว
     */
    public function delete()
    {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $this->id = htmlspecialchars(strip_tags($this->id));
            $stmt->bindParam(1, $this->id);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Database error in Department->delete(): " . $e->getMessage());
            if ($e->getCode() == 23000) { // SQLSTATE for Integrity Constraint Violation (Foreign Key)
                return false; 
            }
            return false;
        }
    }
}