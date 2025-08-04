<?php
// controllers/DepartmentController.php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Department.php';

class DepartmentController
{
    private $db;
    private $department;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
        if (!in_array($_SESSION['role_id'], [1, 2])) {
            $_SESSION['error_message'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->department = new Department($this->db);
    }

    public function index()
    {
        $page_title = "จัดการแผนก";
        $departments_stmt = $this->department->readAll(); // เรียกใช้ Model

        // *** บรรทัดนี้สำคัญ: ตรวจสอบค่าที่ส่งกลับมาจาก readAll() อย่างละเอียด ***
        if ($departments_stmt === null || !$departments_stmt instanceof PDOStatement) {
            // หากเกิดข้อผิดพลาดใน Model (คืนค่า null) หรือไม่ใช่ PDOStatement ที่ถูกต้อง
            // สร้าง empty PDOStatement เพื่อให้ while loop ใน View ไม่เกิด Error
            $stmt = null; // ตั้งเป็น null เพื่อให้ view ตรวจสอบได้
            $num = 0;
            // เพิ่มข้อความ Error เพื่อแจ้งให้ผู้ใช้ทราบถึงปัญหา
            $_SESSION['error_message'] = "ไม่สามารถดึงข้อมูลแผนกได้ โปรดตรวจสอบการเชื่อมต่อฐานข้อมูล ตาราง หรือ PHP Error Log";
        } else {
            // ถ้าดึงข้อมูลสำเร็จ
            $num = $departments_stmt->rowCount();
            $stmt = $departments_stmt; // ส่ง PDOStatement ไปยัง View ด้วยชื่อ $stmt
        }
        
        require_once __DIR__ . '/../views/settings/departments/index.php'; // พาธนี้ถูกต้องแล้ว
    }

    public function create()
    {
        $page_title = "เพิ่มแผนกใหม่";
        $department = (object)[
            'id' => null,
            'name_th' => '',
            'name_en' => '',
            'description' => ''
        ];
        require_once __DIR__ . '/../views/settings/departments/form.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->department->name_th = $_POST['name_th'] ?? '';
            $this->department->name_en = $_POST['name_en'] ?? '';
            $this->department->description = $_POST['description'] ?? '';

            if ($this->department->create()) {
                $_SESSION['success_message'] = "เพิ่มแผนกสำเร็จ";
            } else {
                $_SESSION['error_message'] = "เพิ่มแผนกไม่สำเร็จ";
            }
            header('Location: ' . BASE_URL . '/departments');
            exit();
        }
        header('Location: ' . BASE_URL . '/departments');
        exit();
    }

    public function edit($id = null)
    {
        if (!$id) {
            $_SESSION['error_message'] = "ไม่พบ ID แผนกที่ต้องการแก้ไข";
            header('Location: ' . BASE_URL . '/departments');
            exit();
        }

        $this->department->id = $id;
        $department = $this->department->readOne();

        if (!$department) {
            $_SESSION['error_message'] = "ไม่พบข้อมูลแผนกที่ระบุ";
            header('Location: ' . BASE_URL . '/departments');
            exit();
        }

        $page_title = "แก้ไขแผนก";
        require_once __DIR__ . '/../views/settings/departments/form.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->department->id = $_POST['id'] ?? null;
            $this->department->name_th = $_POST['name_th'] ?? '';
            $this->department->name_en = $_POST['name_en'] ?? '';
            $this->department->description = $_POST['description'] ?? '';

            if (!$this->department->id) {
                $_SESSION['error_message'] = "ไม่พบ ID แผนกสำหรับการอัปเดต";
                header('Location: ' . BASE_URL . '/departments');
                exit();
            }

            if ($this->department->update()) {
                $_SESSION['success_message'] = "อัปเดตแผนกสำเร็จ";
            } else {
                $_SESSION['error_message'] = "อัปเดตแผนกไม่สำเร็จ";
            }
            header('Location: ' . BASE_URL . '/departments');
            exit();
        }
        header('Location: ' . BASE_URL . '/departments');
        exit();
    }

    public function destroy($id = null)
    {
        if (!$id) {
            $_SESSION['error_message'] = "ไม่พบ ID แผนกที่ต้องการลบ";
            header('Location: ' . BASE_URL . '/departments');
            exit();
        }

        $this->department->id = $id;
        if ($this->department->delete()) {
            $_SESSION['success_message'] = "ลบแผนกสำเร็จ";
        } else {
            $_SESSION['error_message'] = "ลบแผนกไม่สำเร็จ (อาจมีพนักงานในแผนกนี้ หรือเกิดข้อผิดพลาดอื่น ๆ)";
        }
        header('Location: ' . BASE_URL . '/departments');
        exit();
    }
}