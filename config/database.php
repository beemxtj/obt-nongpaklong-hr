<?php
// config/database.php

class Database {
    // ระบุข้อมูลการเชื่อมต่อฐานข้อมูลของคุณ
    private $host = "localhost";
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
