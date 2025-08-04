<?php
// config/database.php

class Database {
    // ระบุข้อมูลการเชื่อมต่อฐานข้อมูลของคุณ
    private $host = "localhost";
<<<<<<< HEAD
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
=======
    private $db_name = "hrm_nongpaklong"; // ชื่อฐานข้อมูล
    private $username = "root"; // ชื่อผู้ใช้ฐานข้อมูล
    private $password = ""; // รหัสผ่านฐานข้อมูล
    public $conn;

    // ฟังก์ชันสำหรับรับการเชื่อมต่อฐานข้อมูล
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8"); // ตั้งค่า character set เป็น utf8
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // แสดง error
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
