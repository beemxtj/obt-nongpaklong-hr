<?php
// config/database.php

class Database {
    // ระบุข้อมูลการเชื่อมต่อฐานข้อมูลของคุณ
    private $host = "localhost";
    private $db_name = "hrm_nongpaklong"; // <--- แก้ไขชื่อฐานข้อมูลตรงนี้!
    private $username = "root";     
    private $password = "";     
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            // *** สำคัญ: เพิ่มบรรทัดนี้เพื่อตั้งค่าให้ PDO โยน Exception เมื่อเกิดข้อผิดพลาด ***
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
            // ใน production คุณอาจจะ log error แทนที่จะ echo ออกไปตรงๆ
            die(); // หยุดการทำงานถ้าเชื่อมต่อไม่ได้
        }
        return $this->conn;
    }
}