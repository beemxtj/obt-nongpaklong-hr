<?php
// models/Report.php

<<<<<<< HEAD
class Report
{
    private $conn;

    public function __construct($db)
    {
=======
class Report {
    private $conn;

    public function __construct($db) {
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
        $this->conn = $db;
    }

    // ดึงและประมวลผลข้อมูลสรุปการลงเวลา
<<<<<<< HEAD
    public function getAttendanceSummary($month, $year)
    {
=======
    public function getAttendanceSummary($month, $year) {
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
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
<<<<<<< HEAD
    // ===== ฟังก์ชันใหม่: ดึงข้อมูลใบลงเวลารายเดือน =====
    public function getMonthlyTimesheet($employee_id, $month, $year)
    {
        $timesheet_data = [];
        $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        for ($day = 1; $day <= $num_days; $day++) {
            $current_date_str = "$year-$month-" . str_pad($day, 2, '0', STR_PAD_LEFT);
            $current_date = new DateTime($current_date_str);
            $day_of_week = $current_date->format('N'); // 1 (for Monday) through 7 (for Sunday)

            $daily_record = [
                'date' => $current_date_str,
                'status' => 'วันหยุด', // สถานะเริ่มต้น
                'clock_in' => '-',
                'clock_out' => '-',
                'work_hours' => 0,
                'ot_hours' => 0
            ];

            // 1. ตรวจสอบข้อมูลการลงเวลาทำงาน (attendance_logs)
            $query_att = "SELECT * FROM attendance_logs WHERE employee_id = ? AND DATE(clock_in_time) = ?";
            $stmt_att = $this->conn->prepare($query_att);
            $stmt_att->execute([$employee_id, $current_date_str]);
            $att_log = $stmt_att->fetch(PDO::FETCH_ASSOC);

            if ($att_log) {
                $daily_record['status'] = $att_log['status'];
                $daily_record['clock_in'] = date('H:i:s', strtotime($att_log['clock_in_time']));
                $daily_record['clock_out'] = $att_log['clock_out_time'] ? date('H:i:s', strtotime($att_log['clock_out_time'])) : '-';
                $daily_record['work_hours'] = $att_log['work_hours'];
                $daily_record['ot_hours'] = $att_log['ot_hours'];
            } else {
                // 2. ถ้าไม่พบ ให้ตรวจสอบข้อมูลการลา (leave_requests)
                $query_leave = "SELECT lt.name FROM leave_requests lr JOIN leave_types lt ON lr.leave_type_id = lt.id WHERE lr.employee_id = ? AND ? BETWEEN DATE(lr.start_date) AND DATE(lr.end_date) AND lr.status = 'อนุมัติ'";
                $stmt_leave = $this->conn->prepare($query_leave);
                $stmt_leave->execute([$employee_id, $current_date_str]);
                $leave_log = $stmt_leave->fetch(PDO::FETCH_ASSOC);

                if ($leave_log) {
                    $daily_record['status'] = $leave_log['name'];
                } elseif ($day_of_week < 6) { // 3. ถ้าไม่ใช่วันหยุด (เสาร์-อาทิตย์) และไม่มีข้อมูล ให้ถือว่า "ขาดงาน"
                    $daily_record['status'] = 'ขาดงาน';
                }
            }

            $timesheet_data[] = $daily_record;
        }

        return $timesheet_data;
    }
    /**
     * ดึงข้อมูลรายงานการลาแบบละเอียด พร้อมเงื่อนไขการกรอง
     * @param array $filters - Array ของเงื่อนไข เช่น ['start_date' => ..., 'department_id' => ...]
     * @return PDOStatement
     */
    public function getLeaveReport($filters = [])
    {
        $query = "SELECT
                    lr.id,
                    lr.start_date,
                    lr.end_date,
                    lr.reason,
                    lr.status,
                    DATEDIFF(lr.end_date, lr.start_date) + 1 AS total_days,
                    e.employee_code,
                    CONCAT(e.prefix, e.first_name_th, ' ', e.last_name_th) AS full_name,
                    d.name_th AS department_name,
                    lt.name AS leave_type_name
                  FROM
                    leave_requests lr
                    JOIN employees e ON lr.employee_id = e.id
                    LEFT JOIN departments d ON e.department_id = d.id
                    LEFT JOIN leave_types lt ON lr.leave_type_id = lt.id
                  WHERE 1=1"; // ใช้ 1=1 เพื่อให้ง่ายต่อการต่อ string query

        $params = [];

        // เพิ่มเงื่อนไขตาม filter ที่ได้รับมา
        if (!empty($filters['start_date'])) {
            $query .= " AND lr.start_date >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $query .= " AND lr.end_date <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }
        if (!empty($filters['department_id'])) {
            $query .= " AND e.department_id = :department_id";
            $params[':department_id'] = $filters['department_id'];
        }
        if (!empty($filters['leave_type_id'])) {
            $query .= " AND lr.leave_type_id = :leave_type_id";
            $params[':leave_type_id'] = $filters['leave_type_id'];
        }

        $query .= " ORDER BY lr.start_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }
    /**
     * ดึงข้อมูลสรุปการลาที่ไม่ได้รับค่าจ้างสำหรับ Payroll
     * @param int $month เดือน (1-12)
     * @param int $year ปี (ค.ศ.)
     * @return PDOStatement
     */
    public function getPayrollExportData($month, $year)
    {
        // Query นี้จะหาประเภทการลาที่ชื่อว่า 'ลาไม่รับเงินเดือน'
        // และจะนับผลรวมของจำนวนวันที่ลาในเดือนและปีที่กำหนด
        // จากนั้นจัดกลุ่มตามพนักงานแต่ละคน
        $query = "SELECT
                    e.employee_code,
                    CONCAT(e.prefix, e.first_name_th, ' ', e.last_name_th) AS full_name,
                    SUM(DATEDIFF(lr.end_date, lr.start_date) + 1) AS total_unpaid_days
                  FROM
                    leave_requests lr
                    JOIN employees e ON lr.employee_id = e.id
                    JOIN leave_types lt ON lr.leave_type_id = lt.id
                  WHERE
                    lt.name = 'ลาไม่รับเงินเดือน'
                    AND lr.status = 'อนุมัติ'
                    AND MONTH(lr.start_date) = :month
                    AND YEAR(lr.start_date) = :year
                  GROUP BY
                    e.id, e.employee_code, full_name
                  ORDER BY
                    e.employee_code ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':month', $month);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        return $stmt;
    }
    /**
     * ดึงข้อมูลสรุปแนวโน้มการลาในแต่ละเดือน
     * @param int $year ปี (ค.ศ.)
     * @return array
     */
    public function getLeaveTrendsByMonth($year)
    {
        $query = "SELECT
                    MONTH(start_date) as month,
                    lt.name as leave_type,
                    COUNT(lr.id) as total_requests
                  FROM leave_requests lr
                  JOIN leave_types lt ON lr.leave_type_id = lt.id
                  WHERE YEAR(start_date) = :year AND lr.status = 'อนุมัติ'
                  GROUP BY MONTH(start_date), lt.name
                  ORDER BY month ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ดึงข้อมูลสรุปการลาแยกตามแผนก
     * @param int $year ปี (ค.ศ.)
     * @return array
     */
    public function getLeaveSummaryByDepartment($year)
    {
        $query = "SELECT
                d.name_th as department_name,
                SUM(DATEDIFF(lr.end_date, lr.start_date) + 1) as total_days
              FROM leave_requests lr
              JOIN employees e ON lr.employee_id = e.id
              JOIN departments d ON e.department_id = d.id
              WHERE YEAR(lr.start_date) = :year AND lr.status = 'อนุมัติ'
              GROUP BY d.name_th
              ORDER BY total_days DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
=======
}
?>
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
