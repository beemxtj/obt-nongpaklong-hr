<?php
// controllers/PositionController.php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Position.php';

class PositionController
{
    private $db;
    private $position;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // ตรวจสอบการเข้าสู่ระบบ
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
        
        // ตรวจสอบสิทธิ์ (เฉพาะ Admin และ HR Manager)
        if (!in_array($_SESSION['role_id'], [1, 2])) {
            $_SESSION['error_message'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->position = new Position($this->db);
    }

    /**
     * แสดงรายการตำแหน่งทั้งหมด
     */
    public function index()
    {
        $page_title = "จัดการตำแหน่ง";
        $positions_stmt = $this->position->readAll();

        if ($positions_stmt === null || !$positions_stmt instanceof PDOStatement) {
            $stmt = null;
            $num = 0;
            $_SESSION['error_message'] = "ไม่สามารถดึงข้อมูลตำแหน่งได้ โปรดตรวจสอบการเชื่อมต่อฐานข้อมูล";
        } else {
            $num = $positions_stmt->rowCount();
            $stmt = $positions_stmt;
        }
        
        require_once __DIR__ . '/../views/settings/positions/index.php';
    }

    /**
     * แสดงฟอร์มเพิ่มตำแหน่งใหม่
     */
    public function create()
    {
        $page_title = "เพิ่มตำแหน่งใหม่";
        $position = (object)[
            'id' => null,
            'name_th' => '',
            'name_en' => '',
            'description' => ''
        ];
        require_once __DIR__ . '/../views/settings/positions/form.php';
    }

    /**
     * บันทึกตำแหน่งใหม่
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // รับข้อมูลจากฟอร์ม
            $this->position->name_th = trim($_POST['name_th'] ?? '');
            $this->position->name_en = trim($_POST['name_en'] ?? '');
            $this->position->description = trim($_POST['description'] ?? '');

            // ตรวจสอบข้อมูลที่จำเป็น
            if (empty($this->position->name_th)) {
                $_SESSION['error_message'] = "กรุณากรอกชื่อตำแหน่ง (ไทย)";
                header('Location: ' . BASE_URL . '/positions/create');
                exit();
            }

            // ตรวจสอบความยาวข้อมูล
            if (strlen($this->position->name_th) > 100) {
                $_SESSION['error_message'] = "ชื่อตำแหน่ง (ไทย) ต้องไม่เกิน 100 ตัวอักษร";
                header('Location: ' . BASE_URL . '/positions/create');
                exit();
            }

            if (!empty($this->position->name_en) && strlen($this->position->name_en) > 100) {
                $_SESSION['error_message'] = "ชื่อตำแหน่ง (อังกฤษ) ต้องไม่เกิน 100 ตัวอักษร";
                header('Location: ' . BASE_URL . '/positions/create');
                exit();
            }

            // บันทึกข้อมูล
            if ($this->position->create()) {
                $_SESSION['success_message'] = "เพิ่มตำแหน่งสำเร็จ";
            } else {
                $_SESSION['error_message'] = "เพิ่มตำแหน่งไม่สำเร็จ";
            }
            
            header('Location: ' . BASE_URL . '/positions');
            exit();
        }
        
        header('Location: ' . BASE_URL . '/positions');
        exit();
    }

    /**
     * แสดงฟอร์มแก้ไขตำแหน่ง
     */
    public function edit($id = null)
    {
        if (!$id || !is_numeric($id)) {
            $_SESSION['error_message'] = "ไม่พบ ID ตำแหน่งที่ต้องการแก้ไข";
            header('Location: ' . BASE_URL . '/positions');
            exit();
        }

        $this->position->id = $id;
        $position = $this->position->readOne();

        if (!$position) {
            $_SESSION['error_message'] = "ไม่พบข้อมูลตำแหน่งที่ระบุ";
            header('Location: ' . BASE_URL . '/positions');
            exit();
        }

        $page_title = "แก้ไขตำแหน่ง";
        require_once __DIR__ . '/../views/settings/positions/form.php';
    }

    /**
     * อัปเดตตำแหน่ง
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->position->id = $_POST['id'] ?? null;
            $this->position->name_th = trim($_POST['name_th'] ?? '');
            $this->position->name_en = trim($_POST['name_en'] ?? '');
            $this->position->description = trim($_POST['description'] ?? '');

            // ตรวจสอบ ID
            if (!$this->position->id || !is_numeric($this->position->id)) {
                $_SESSION['error_message'] = "ไม่พบ ID ตำแหน่งสำหรับการอัปเดต";
                header('Location: ' . BASE_URL . '/positions');
                exit();
            }

            // ตรวจสอบข้อมูลที่จำเป็น
            if (empty($this->position->name_th)) {
                $_SESSION['error_message'] = "กรุณากรอกชื่อตำแหน่ง (ไทย)";
                header('Location: ' . BASE_URL . '/positions/edit/' . $this->position->id);
                exit();
            }

            // ตรวจสอบความยาวข้อมูล
            if (strlen($this->position->name_th) > 100) {
                $_SESSION['error_message'] = "ชื่อตำแหน่ง (ไทย) ต้องไม่เกิน 100 ตัวอักษร";
                header('Location: ' . BASE_URL . '/positions/edit/' . $this->position->id);
                exit();
            }

            if (!empty($this->position->name_en) && strlen($this->position->name_en) > 100) {
                $_SESSION['error_message'] = "ชื่อตำแหน่ง (อังกฤษ) ต้องไม่เกิน 100 ตัวอักษร";
                header('Location: ' . BASE_URL . '/positions/edit/' . $this->position->id);
                exit();
            }

            // อัปเดตข้อมูล
            if ($this->position->update()) {
                $_SESSION['success_message'] = "อัปเดตตำแหน่งสำเร็จ";
            } else {
                $_SESSION['error_message'] = "อัปเดตตำแหน่งไม่สำเร็จ";
            }
            
            header('Location: ' . BASE_URL . '/positions');
            exit();
        }
        
        header('Location: ' . BASE_URL . '/positions');
        exit();
    }

    /**
     * ลบตำแหน่ง
     */
    public function destroy($id = null)
    {
        if (!$id || !is_numeric($id)) {
            $_SESSION['error_message'] = "ไม่พบ ID ตำแหน่งที่ต้องการลบ";
            header('Location: ' . BASE_URL . '/positions');
            exit();
        }

        $this->position->id = $id;

        // ตรวจสอบว่าตำแหน่งมีการใช้งานอยู่หรือไม่
        if ($this->position->isInUse()) {
            $_SESSION['error_message'] = "ไม่สามารถลบตำแหน่งได้ เนื่องจากมีพนักงานใช้ตำแหน่งนี้อยู่";
            header('Location: ' . BASE_URL . '/positions');
            exit();
        }

        if ($this->position->delete()) {
            $_SESSION['success_message'] = "ลบตำแหน่งสำเร็จ";
        } else {
            $_SESSION['error_message'] = "ลบตำแหน่งไม่สำเร็จ";
        }
        
        header('Location: ' . BASE_URL . '/positions');
        exit();
    }

    /**
     * ค้นหาตำแหน่ง
     */
    public function search()
    {
        $search_term = $_GET['search'] ?? '';
        $page_title = "ค้นหาตำแหน่ง: " . htmlspecialchars($search_term);

        if (!empty($search_term)) {
            $positions_stmt = $this->position->search($search_term);
            
            if ($positions_stmt === null || !$positions_stmt instanceof PDOStatement) {
                $stmt = null;
                $num = 0;
                $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการค้นหา";
            } else {
                $num = $positions_stmt->rowCount();
                $stmt = $positions_stmt;
            }
        } else {
            // ถ้าไม่มีคำค้นหา ให้แสดงทั้งหมด
            $this->index();
            return;
        }

        require_once __DIR__ . '/../views/settings/positions/index.php';
    }
}