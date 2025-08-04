<?php
// controllers/DashboardController.php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/../models/Leave.php';
require_once __DIR__ . '/../models/Dashboard.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Report.php';

class DashboardController {

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_URL . '/login'); exit(); }
    }

    public function index() {
        $database = new Database();
        $db = $database->getConnection();
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['role_id'];

        if (in_array($userRole, [1, 2, 3])) { // Admin, HR, Supervisor
            // **ปรับปรุง: เปลี่ยนชื่อหัวข้อเป็นภาษาไทย**
            $page_title = "ภาพรวมระบบ";
            $dashboard_model = new Dashboard($db);
            
            // --- ข้อมูลเดิม ---
            $stats = $dashboard_model->getAdminStats();
            $today_attendance_list = $dashboard_model->getTodayAttendanceListWithCode();

            // --- ข้อมูลใหม่สำหรับกราฟ ---
            $late_chart_data = $dashboard_model->getLateCountLast30Days();
            $absent_chart_data = $dashboard_model->getAbsentCountLast30Days();

            // แปลงเป็น JSON เพื่อส่งให้ Javascript
            $late_chart_data_json = json_encode($late_chart_data);
            $absent_chart_data_json = json_encode($absent_chart_data);

            require_once 'views/dashboard/admin.php';

        } else {
            // --- สำหรับพนักงานทั่วไป ---
            $page_title = "แดชบอร์ด";
            $attendance = new Attendance($db);
            $today_attendance_stmt = $attendance->getTodayAttendance($userId);
            $today_log = $today_attendance_stmt->fetch(PDO::FETCH_ASSOC);

            $leave = new Leave($db);
            $leave_balances = $leave->getLeaveBalances($userId);

            require_once 'views/dashboard/index.php';
        }
    }
/**
     * ===== ฟังก์ชันใหม่: แสดงหน้า Analytics Dashboard =====
     */
    public function analytics() {
        // จำกัดสิทธิ์เฉพาะ Admin หรือ HR
        if (!in_array($_SESSION['role_id'], [1, 2, 3])) { exit('Access Denied'); }

        $page_title = "ภาพรวมเชิงวิเคราะห์";
        $database = new Database();
        $db = $database->getConnection();
        $report_model = new Report($db);
        
        $current_year = date('Y');

        // ดึงข้อมูลสำหรับสร้างกราฟ
        $leave_trends = $report_model->getLeaveTrendsByMonth($current_year);
        $department_summary = $report_model->getLeaveSummaryByDepartment($current_year);

        // แปลงข้อมูล PHP array เป็น JSON เพื่อให้ Javascript ใช้งานได้ง่าย
        $leave_trends_json = json_encode($leave_trends);
        $department_summary_json = json_encode($department_summary);

        require_once 'views/dashboard/analytics.php';
    }
}
?>
