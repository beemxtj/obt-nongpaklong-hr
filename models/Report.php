<?php
// models/Report.php

class Report {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // ดึงและประมวลผลข้อมูลสรุปการลงเวลา
    public function getAttendanceSummary($month, $year) {
        // 1. ดึงรายชื่อพนักงานทั้งหมดที่ยังทำงานอยู่
        $query_employees = "SELECT id, employee_code, CONCAT(prefix, first_name_th, ' ', last_name_th) as full_name 
                            FROM employees 
                            WHERE status IN ('ทำงาน', 'ทดลองงาน')";
        $stmt_employees = $this->conn->prepare($query_employees);
        $stmt_employees->execute();
        $employees = $stmt_employees->fetchAll(PDO::FETCH_ASSOC);

        $summary_data = [];

        // 2. Loop พนักงานแต่ละคนเพื่อดึงข้อมูลสรุป
        foreach ($employees as $employee) {
            // นับวันทำงานปกติ
            $query_work = "SELECT COUNT(id) as count FROM attendance_logs WHERE employee_id = ? AND MONTH(clock_in_time) = ? AND YEAR(clock_in_time) = ? AND status = 'ปกติ'";
            $stmt_work = $this->conn->prepare($query_work);
            $stmt_work->execute([$employee['id'], $month, $year]);
            $work_days = $stmt_work->fetchColumn();

            // นับวันมาสาย
            $query_late = "SELECT COUNT(id) as count FROM attendance_logs WHERE employee_id = ? AND MONTH(clock_in_time) = ? AND YEAR(clock_in_time) = ? AND status = 'สาย'";
            $stmt_late = $this->conn->prepare($query_late);
            $stmt_late->execute([$employee['id'], $month, $year]);
            $late_days = $stmt_late->fetchColumn();

            // นับวันลา (ที่อนุมัติแล้ว)
            $query_leave = "SELECT SUM(DATEDIFF(DATE(end_date), DATE(start_date)) + 1) as count FROM leave_requests WHERE employee_id = ? AND MONTH(start_date) = ? AND YEAR(start_date) = ? AND status = 'อนุมัติ'";
            $stmt_leave = $this->conn->prepare($query_leave);
            $stmt_leave->execute([$employee['id'], $month, $year]);
            $leave_days = $stmt_leave->fetchColumn() ?? 0;

            $summary_data[] = [
                'employee_code' => $employee['employee_code'],
                'full_name' => $employee['full_name'],
                'work_days' => $work_days,
                'late_days' => $late_days,
                'leave_days' => $leave_days,
                'total_present' => $work_days + $late_days,
            ];
        }

        return $summary_data;
    }
}
?>
