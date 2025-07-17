<?php
// controllers/RoleController.php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Role.php';
require_once __DIR__ . '/../config/database.php';

class RoleController {

    private $db;
    private $role;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_URL . '/login'); exit(); }
        
        if ($_SESSION['role_id'] != 1) {
            $_SESSION['error_message'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
        
        $database = new Database();
        $this->db = $database->getConnection();
        $this->role = new Role($this->db);
    }

    public function index() {
        $page_title = "จัดการสิทธิ์ผู้ใช้งาน";
        $stmt = $this->role->read();
        $num = $stmt->rowCount();
        require_once 'views/roles/index.php';
    }

    public function create() {
        $page_title = "เพิ่มบทบาทใหม่";
        $role = $this->role;
        $permissions_list = $this->getPermissionsList();
        require_once 'views/roles/form.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->role->role_name = $_POST['role_name'];
            $permissions = isset($_POST['permissions']) ? json_encode($_POST['permissions']) : json_encode([]);
            $this->role->permissions = $permissions;

            if ($this->role->create()) {
                $_SESSION['success_message'] = "สร้างบทบาทใหม่สำเร็จ";
                header('Location: ' . BASE_URL . '/role');
            } else {
                $_SESSION['error_message'] = "ไม่สามารถสร้างบทบาทได้";
                header('Location: ' . BASE_URL . '/role/create');
            }
            exit();
        }
    }
    
    // ===== ฟังก์ชันใหม่: แสดงฟอร์มแก้ไข =====
    public function edit($id) {
        $page_title = "แก้ไขบทบาท";
        $this->role->id = $id;
        $this->role->readOne(); // ดึงข้อมูลเดิม
        $role = $this->role; // ส่ง object ที่มีข้อมูลแล้วไปให้ view
        $permissions_list = $this->getPermissionsList();
        require_once 'views/roles/form.php';
    }

    // ===== ฟังก์ชันใหม่: อัปเดตข้อมูล =====
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->role->id = $_POST['id'];
            $this->role->role_name = $_POST['role_name'];
            $permissions = isset($_POST['permissions']) ? json_encode($_POST['permissions']) : json_encode([]);
            $this->role->permissions = $permissions;

            if ($this->role->update()) {
                $_SESSION['success_message'] = "อัปเดตข้อมูลบทบาทสำเร็จ";
                header('Location: ' . BASE_URL . '/role');
            } else {
                $_SESSION['error_message'] = "ไม่สามารถอัปเดตข้อมูลได้";
                header('Location: ' . BASE_URL . '/role/edit/' . $_POST['id']);
            }
            exit();
        }
    }

    // ===== ฟังก์ชันใหม่: ลบข้อมูล =====
    public function destroy($id) {
        $this->role->id = $id;
        if ($this->role->delete()) {
            $_SESSION['success_message'] = "ลบข้อมูลบทบาทสำเร็จ";
        } else {
            $_SESSION['error_message'] = "ไม่สามารถลบข้อมูลได้";
        }
        header('Location: ' . BASE_URL . '/role');
        exit();
    }
    
    private function getPermissionsList() {
        return [
            'manage_employees' => 'จัดการข้อมูลพนักงาน (CRUD)',
            'manage_attendance' => 'จัดการข้อมูลลงเวลา',
            'approve_leave' => 'อนุมัติใบลา',
            'manage_settings' => 'จัดการตั้งค่าระบบ',
            'view_reports' => 'ดูรายงานสรุป'
        ];
    }
}
?>
