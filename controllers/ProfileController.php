<?php
// controllers/ProfileController.php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../config/database.php';

class ProfileController {

    private $db;
    private $employee;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_URL . '/login'); exit(); }
        
        $database = new Database();
        $this->db = $database->getConnection();
        $this->employee = new Employee($this->db);
    }

    // แสดงหน้าโปรไฟล์
    public function index() {
        $page_title = "โปรไฟล์ของฉัน";
        
        $this->employee->id = $_SESSION['user_id'];
        $this->employee->readOne(); // ดึงข้อมูลของคนที่ล็อกอินอยู่
        $employee = $this->employee;

        require_once 'views/profile/index.php';
    }

    // อัปเดตข้อมูลโปรไฟล์
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->employee->id = $_SESSION['user_id'];
            
            // รับข้อมูลที่อนุญาตให้แก้ไขได้
            $this->employee->first_name_th = $_POST['first_name_th'];
            $this->employee->last_name_th = $_POST['last_name_th'];
            $this->employee->phone_number = $_POST['phone_number'];
            
            // อัปเดตรหัสผ่าน (ถ้ามีการกรอกใหม่)
            if (!empty($_POST['password'])) {
                $this->employee->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            } else {
                $this->employee->password = null; // ไม่ต้องอัปเดต
            }

            // (ส่วนนี้จะถูกใช้ใน Model เพื่ออัปเดตเฉพาะฟิลด์ที่ต้องการ)
            if ($this->employee->updateProfile()) {
                $_SESSION['success_message'] = "อัปเดตโปรไฟล์สำเร็จ";
            } else {
                $_SESSION['error_message'] = "ไม่สามารถอัปเดตโปรไฟล์ได้";
            }
            
            header('Location: ' . BASE_URL . '/profile');
            exit();
        }
    }
}
?>
