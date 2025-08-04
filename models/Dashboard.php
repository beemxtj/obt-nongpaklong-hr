<?php
// models/Dashboard.php

class Dashboard {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // ดึงข้อมูลสถิติภาพรวมสำหรับ Admin
    public function getAdminStats() {
        $stats = [];
        $today_date = date('Y-m-d');

        // 1. จำนวนพนักงานทั้งหมด (ที่ยังทำงาน)
        $query_total = "SELECT COUNT(id) FROM employees WHERE status IN ('ทำงาน', 'ทดลองงาน')";
        $stmt_total = $this->conn->prepare($query_total);
        $stmt_total->execute();
        $stats['total_employees'] = $stmt_total->fetchColumn();

        // 2. จำนวนพนักงานที่ลงเวลาเข้าวันนี้
        $query_present = "SELECT COUNT(id) FROM attendance_logs WHERE DATE(clock_in_time) = ?";
        $stmt_present = $this->conn->prepare($query_present);
        $stmt_present->execute([$today_date]);
        $stats['present_today'] = $stmt_present->fetchColumn();

        // 3. จำนวนพนักงานที่มาสายวันนี้
        $query_late = "SELECT COUNT(id) FROM attendance_logs WHERE DATE(clock_in_time) = ? AND status = 'สาย'";
        $stmt_late = $this->conn->prepare($query_late);
        $stmt_late->execute([$today_date]);
        $stats['late_today'] = $stmt_late->fetchColumn();

        // 4. จำนวนพนักงานที่ลาวันนี้
        $query_leave = "SELECT COUNT(DISTINCT employee_id) FROM leave_requests WHERE ? BETWEEN DATE(start_date) AND DATE(end_date) AND status = 'อนุมัติ'";
        $stmt_leave = $this->conn->prepare($query_leave);
        $stmt_leave->execute([$today_date]);
        $stats['on_leave_today'] = $stmt_leave->fetchColumn();

        // 5. จำนวนใบลาที่รออนุมัติ
        $query_pending = "SELECT COUNT(id) FROM leave_requests WHERE status = 'รออนุมัติ'";
        $stmt_pending = $this->conn->prepare($query_pending);
        $stmt_pending->execute();
        $stats['pending_leaves'] = $stmt_pending->fetchColumn();

        return $stats;
    }

    // ===== ฟังก์ชันดึงรายการลงเวลาทั้งหมดของวันนี้ (แก้ไข) =====
    public function getTodayAttendanceList() {
        $today_date = date('Y-m-d');
        $query = "SELECT 
                    e.first_name_th, 
                    e.last_name_th, 
                    a.clock_in_time, 
                    a.clock_out_time,
                    a.clock_in_image_path,
                    a.clock_out_image_path,
                    a.clock_in_latitude,
                    a.clock_in_longitude,
                    a.clock_out_latitude,
                    a.clock_out_longitude,
                    p.name_th as position_name
                  FROM attendance_logs a
                  JOIN employees e ON a.employee_id = e.id
                  LEFT JOIN positions p ON e.position_id = p.id
                  WHERE DATE(a.clock_in_time) = ?
                  ORDER BY a.clock_in_time ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$today_date]);
        return $stmt;
    }

    /**
     * ===== ฟังก์ชันฉบับปรับปรุง: ดึงรายการลงเวลาพร้อมรหัสพนักงาน =====
     */
    public function getTodayAttendanceListWithCode() {
        $today_date = date('Y-m-d');
        $query = "SELECT 
                    e.employee_code, e.first_name_th, e.last_name_th, 
                    a.clock_in_time, a.clock_out_time
                  FROM attendance_logs a
                  JOIN employees e ON a.employee_id = e.id
                  WHERE DATE(a.clock_in_time) = ?
                  ORDER BY a.clock_in_time ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$today_date]);
        return $stmt;
    }

    /**
     * ===== ฟังก์ชันใหม่: ดึงข้อมูลจำนวนคนมาสาย 30 วันย้อนหลัง =====
     */
    public function getLateCountLast30Days() {
        $query = "SELECT DATE(clock_in_time) as date, COUNT(id) as count 
                  FROM attendance_logs 
                  WHERE status = 'สาย' AND clock_in_time >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                  GROUP BY DATE(clock_in_time) 
                  ORDER BY date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ===== ฟังก์ชันใหม่: ดึงข้อมูลจำนวนคนขาดงาน 30 วันย้อนหลัง =====
     * หมายเหตุ: Logic การนับคนขาดงานอาจซับซ้อนกว่านี้
     * ในที่นี้จะใช้การนับจาก status 'ขาดงาน' เป็นตัวอย่าง
     */
    public function getAbsentCountLast30Days() {
        // This is a placeholder logic. A more complex query would be needed to
        // accurately determine absence by comparing expected workdays with actual attendance.
        $query = "SELECT DATE(clock_in_time) as date, COUNT(id) as count 
                  FROM attendance_logs 
                  WHERE status = 'ขาดงาน' AND clock_in_time >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                  GROUP BY DATE(clock_in_time) 
                  ORDER BY date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
