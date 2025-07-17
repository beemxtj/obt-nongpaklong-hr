<?php
// controllers/LeaveController.php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Leave.php';
require_once __DIR__ . '/../models/Employee.php'; 
require_once __DIR__ . '/../models/Notification.php'; 
require_once __DIR__ . '/../config/database.php';

class LeaveController
{

    private $db;
    private $leave;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->leave = new Leave($this->db);
    }

    // แสดงหน้าฟอร์มสำหรับยื่นใบลา
    public function create()
    {
        $page_title = "ยื่นใบลา";

        // ดึงข้อมูลประเภทการลาสำหรับ Dropdown
        $leave_types = $this->leave->getLeaveTypes();

        require_once 'views/leave/create.php';
    }

    // บันทึกคำขอการลา
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->leave->employee_id = $_SESSION['user_id'];
            $this->leave->leave_type_id = $_POST['leave_type_id'];
            $this->leave->start_date = $_POST['start_date'];
            $this->leave->end_date = $_POST['end_date'];
            $this->leave->reason = $_POST['reason'];
            
            // --- ส่วนที่เพิ่ม: จัดการไฟล์แนบ ---
            $attachment_path = null;
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
                $target_dir = "uploads/attachments/";
                // สร้างโฟลเดอร์ถ้ายังไม่มี
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                // สร้างชื่อไฟล์ใหม่เพื่อป้องกันการซ้ำกัน
                $file_extension = pathinfo($_FILES["attachment"]["name"], PATHINFO_EXTENSION);
                $file_name = "leave_" . $this->leave->employee_id . "_" . time() . "." . $file_extension;
                $target_file = $target_dir . $file_name;

                // ย้ายไฟล์ที่อัปโหลดไปยังโฟลเดอร์เป้าหมาย
                if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
                    $attachment_path = $target_file;
                }
            }
            $this->leave->attachment_path = $attachment_path;
            // ------------------------------------

            if ($this->leave->createLeaveRequest()) {
                // --- ส่วนที่เพิ่ม: สร้าง Notification ---
                $employee = new Employee($this->db);
                $employee->id = $_SESSION['user_id'];
                $employee->readOne(); // ดึงข้อมูลพนักงานเพื่อหา supervisor_id

                if ($employee->supervisor_id) {
                    $notification = new Notification($this->db);
                    $notification->user_id = $employee->supervisor_id;
                    $notification->message = "มีคำขอใบลาใหม่จากคุณ " . $_SESSION['user_name'];
                    $notification->link = BASE_URL . '/leave/approval';
                    $notification->create();
                }
                // ------------------------------------

                $_SESSION['success_message'] = "ยื่นใบลาสำเร็จ";
                header('Location: ' . BASE_URL . '/leave/history');
            } else {
                // ...
            }
            exit();
        }
    }

    // เพิ่มฟังก์ชัน history()
    public function history()
    {
        $page_title = "ประวัติการลา";
        $stmt = $this->leave->readHistoryByEmployee($_SESSION['user_id']);
        $num = $stmt->rowCount();
        require_once 'views/leave/history.php';
    }
    // ===== ฟังก์ชันใหม่: แสดงหน้ารายการรออนุมัติ =====
    public function approval() {
        $supervisor_id = $_SESSION['user_id'];
        $stmt = $this->leave->getPendingRequestsBySupervisor($supervisor_id);
        $num = $stmt->rowCount();
        require_once 'views/leave/approval.php';
    }

    // ===== ฟังก์ชันใหม่: อนุมัติใบลา =====
    public function approve($id) {
        $this->leave->id = $id;
        $leave_request = $this->leave->readOne();
        
        if ($this->leave->updateStatus('อนุมัติ')) {
            $notification = new Notification($this->db);
            $notification->user_id = $leave_request['employee_id'];
            $notification->message = "ใบลาของคุณได้รับการอนุมัติแล้ว";
            $notification->link = BASE_URL . '/leave/history';
            $notification->create();
            $_SESSION['success_message'] = "อนุมัติใบลาเรียบร้อยแล้ว";
        }
        header('Location: ' . BASE_URL . '/leave/approval');
        exit();
    }

    // ===== ฟังก์ชันใหม่: ไม่อนุมัติใบลา =====
    public function reject($id) {
        $this->leave->id = $id;
        if ($this->leave->updateStatus('ไม่อนุมัติ')) {
            $_SESSION['success_message'] = "ปฏิเสธใบลาเรียบร้อยแล้ว";
        } else {
            $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการปฏิเสธใบลา";
        }
        header('Location: ' . BASE_URL . '/leave/approval');
        exit();
    }
}
?>