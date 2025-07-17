<?php
// controllers/DashboardController.php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/../models/Leave.php';
require_once __DIR__ . '/../config/database.php';

class DashboardController {

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_URL . '/login'); exit(); }
    }

    public function index() {
        $userName = $_SESSION['user_name'] ?? 'ผู้ใช้งาน';
        $userId = $_SESSION['user_id'];

        $database = new Database();
        $db = $database->getConnection();
        
        // ดึงข้อมูลการลงเวลา
        $attendance = new Attendance($db);
        $today_attendance_stmt = $attendance->getTodayAttendance($userId);
        $today_log = $today_attendance_stmt->fetch(PDO::FETCH_ASSOC);

        // ===== จุดที่แก้ไข: ดึงข้อมูลวันลาคงเหลือ =====
        $leave = new Leave($db);
        $leave_balances = $leave->getLeaveBalances($userId);
        // ===========================================

        require_once 'views/dashboard/index.php';
    }
}
?>