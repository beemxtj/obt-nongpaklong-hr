<?php
// controllers/AttendanceController.php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/../config/database.php';

class AttendanceController
{

    private $db;
    private $attendance;

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
        $this->attendance = new Attendance($this->db);
    }

    // ฟังก์ชันสำหรับบันทึกเวลาเข้างาน
    public function clockIn()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->attendance->employee_id = $_SESSION['user_id'];
            $this->attendance->clock_in_latitude = $_POST['latitude'] ?? null;
            $this->attendance->clock_in_longitude = $_POST['longitude'] ?? null;
            $this->attendance->clock_in_image_data = $_POST['image_data'] ?? null;

            // (ในอนาคต) สามารถเพิ่มการรับ path รูปภาพที่สแกนใบหน้าได้ที่นี่
            // $this->attendance->clock_in_image_path = ...

            if ($this->attendance->createClockIn()) {
                $_SESSION['success_message'] = "บันทึกเวลาเข้างานสำเร็จ!";
            } else {
                $_SESSION['error_message'] = "ไม่สามารถบันทึกเวลาได้ หรืออาจจะมีการลงเวลาไปแล้ว";
            }
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
    }

    // ===== สำหรับบันทึกเวลาออกงาน =====
    public function clockOut()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->attendance->employee_id = $_SESSION['user_id'];
            $this->attendance->clock_out_latitude = $_POST['latitude_out'] ?? null;
            $this->attendance->clock_out_longitude = $_POST['longitude_out'] ?? null;
            $this->attendance->clock_out_image_data = $_POST['image_data_out'] ?? null;

            if ($this->attendance->createClockOut()) {
                $_SESSION['success_message'] = "บันทึกเวลาออกงานสำเร็จ!";
            } else {
                $_SESSION['error_message'] = "ไม่สามารถบันทึกเวลาออกงานได้";
            }
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
    }
    // ===== แสดงหน้าประวัติการลงเวลา =====
    public function history()
    {
        $page_title = "ประวัติการลงเวลา";
        $employee_id = $_SESSION['user_id'];
        $stmt = $this->attendance->readHistoryByEmployee($employee_id);
        $num = $stmt->rowCount();
        // ไม่ต้องแก้ไขอะไรเพิ่มเติมที่นี่ เพราะ View ใหม่จัดการข้อมูลได้
        require_once 'views/attendance/history.php';
    }
}
