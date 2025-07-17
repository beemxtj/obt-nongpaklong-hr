<?php
// controllers/MovementController.php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Movement.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../config/database.php';

class MovementController {

    private $db;
    private $movement;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_URL . '/login'); exit(); }
        
        // จำกัดสิทธิ์ให้ HR หรือ Admin
        if (!in_array($_SESSION['role_id'], [1, 2])) {
            $_SESSION['error_message'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
        
        $database = new Database();
        $this->db = $database->getConnection();
        $this->movement = new Movement($this->db);
    }

    // แสดงฟอร์มสำหรับบันทึกความเคลื่อนไหว
    public function create() {
        $page_title = "บันทึกความเคลื่อนไหวพนักงาน";
        
        $employee_model = new Employee($this->db);
        $employees = $employee_model->read(); // ดึงรายชื่อพนักงานทั้งหมด

        require_once 'views/movements/create.php';
    }

    // บันทึกข้อมูลความเคลื่อนไหว
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->movement->employee_id = $_POST['employee_id'];
            $this->movement->effective_date = $_POST['effective_date'];
            $this->movement->movement_type = $_POST['movement_type'];
            $this->movement->details = $_POST['details'];
            $this->movement->created_by = $_SESSION['user_id'];

            if ($this->movement->create()) {
                // (Optional) อาจจะมีการอัปเดตข้อมูลในตาราง employees ด้วย
                // เช่น ถ้าเป็นการปรับตำแหน่ง ก็ต้องไปอัปเดต position_id ในตาราง employees
                $_SESSION['success_message'] = "บันทึกข้อมูลความเคลื่อนไหวสำเร็จ";
            } else {
                $_SESSION['error_message'] = "ไม่สามารถบันทึกข้อมูลได้";
            }
            header('Location: ' . BASE_URL . '/movement/create');
            exit();
        }
    }
}
?>
