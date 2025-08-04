<?php
// models/Position.php

class Position
{
    private $conn;
    private $table_name = "positions";

    public $id;
    public $name_th;
    public $name_en;
    public $description;
    public $created_at;
    public $updated_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * อ่านข้อมูลตำแหน่งทั้งหมด
     * @return PDOStatement|null คืนค่า PDOStatement ถ้าสำเร็จ, null ถ้าเกิดข้อผิดพลาด
     */
    public function readAll()
    {
        try {
            $query = "SELECT id, name_th, name_en, description, created_at, updated_at 
                     FROM " . $this->table_name . " 
                     ORDER BY name_th ASC";
            $stmt = $this->conn->prepare($query);
            
            if ($stmt === false) {
                error_log("Failed to prepare statement for Position->readAll(): " . implode(":", $this->conn->errorInfo()));
                return null; 
            }

            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database error in Position->readAll(): " . $e->getMessage());
            return null;
        }
    }

    /**
     * อ่านข้อมูลตำแหน่งเดียว
     * @return Position|false คืนค่า object ของ Position ถ้าพบ, false ถ้าไม่พบ
     */
    public function readOne()
    {
        try {
            $query = "SELECT id, name_th, name_en, description, created_at, updated_at 
                     FROM " . $this->table_name . " 
                     WHERE id = ? LIMIT 0,1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $this->name_th = $row['name_th'];
                $this->name_en = $row['name_en'];
                $this->description = $row['description'];
                $this->created_at = $row['created_at'];
                $this->updated_at = $row['updated_at'];
                return $this; 
            }
            return false; 
        } catch (PDOException $e) {
            error_log("Database error in Position->readOne(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * สร้างตำแหน่งใหม่
     * @return bool true ถ้าสำเร็จ, false ถ้าล้มเหลว
     */
    public function create()
    {
        try {
            $query = "INSERT INTO " . $this->table_name . " 
                     SET name_th=:name_th, name_en=:name_en, description=:description";
            $stmt = $this->conn->prepare($query);

            // ทำความสะอาดข้อมูล
            $this->name_th = htmlspecialchars(strip_tags($this->name_th));
            $this->name_en = htmlspecialchars(strip_tags($this->name_en));
            $this->description = htmlspecialchars(strip_tags($this->description));

            // ตรวจสอบข้อมูลที่จำเป็น
            if (empty(trim($this->name_th))) {
                error_log("Position create failed: name_th is required");
                return false;
            }

            $stmt->bindParam(":name_th", $this->name_th);
            $stmt->bindParam(":name_en", $this->name_en);
            $stmt->bindParam(":description", $this->description);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Database error in Position->create(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * อัปเดตตำแหน่ง
     * @return bool true ถ้าสำเร็จ, false ถ้าล้มเหลว
     */
    public function update()
    {
        try {
            $query = "UPDATE " . $this->table_name . " 
                     SET name_th=:name_th, name_en=:name_en, description=:description 
                     WHERE id=:id";
            $stmt = $this->conn->prepare($query);

            // ทำความสะอาดข้อมูล
            $this->name_th = htmlspecialchars(strip_tags($this->name_th));
            $this->name_en = htmlspecialchars(strip_tags($this->name_en));
            $this->description = htmlspecialchars(strip_tags($this->description));
            $this->id = htmlspecialchars(strip_tags($this->id));

            // ตรวจสอบข้อมูลที่จำเป็น
            if (empty(trim($this->name_th))) {
                error_log("Position update failed: name_th is required");
                return false;
            }

            $stmt->bindParam(':name_th', $this->name_th);
            $stmt->bindParam(':name_en', $this->name_en);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':id', $this->id);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Database error in Position->update(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * ลบตำแหน่ง
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
            error_log("Database error in Position->delete(): " . $e->getMessage());
            // ตรวจสอบ Foreign Key Constraint Violation
            if ($e->getCode() == 23000) {
                return false; 
            }
            return false;
        }
    }

    /**
     * ตรวจสอบว่าตำแหน่งมีการใช้งานอยู่หรือไม่
     * @return bool true ถ้ามีการใช้งาน, false ถ้าไม่มี
     */
    public function isInUse()
    {
        try {
            // ตรวจสอบในตาราง employees (สมมติว่ามีตาราง employees ที่มี position_id)
            $query = "SELECT COUNT(*) as count FROM employees WHERE position_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return ($row['count'] > 0);
        } catch (PDOException $e) {
            error_log("Database error in Position->isInUse(): " . $e->getMessage());
            return true; // ถ้าเกิดข้อผิดพลาด ให้ถือว่ามีการใช้งานเพื่อความปลอดภัย
        }
    }

    /**
     * ค้นหาตำแหน่งตามชื่อ
     * @param string $search_term คำค้นหา
     * @return PDOStatement|null
     */
    public function search($search_term)
    {
        try {
            $query = "SELECT id, name_th, name_en, description, created_at, updated_at 
                     FROM " . $this->table_name . " 
                     WHERE name_th LIKE :search OR name_en LIKE :search 
                     ORDER BY name_th ASC";
            $stmt = $this->conn->prepare($query);
            
            $search_param = "%{$search_term}%";
            $stmt->bindParam(":search", $search_param);
            $stmt->execute();
            
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database error in Position->search(): " . $e->getMessage());
            return null;
        }
    }
}