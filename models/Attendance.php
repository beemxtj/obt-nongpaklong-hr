<?php
<<<<<<< HEAD
// models/Attendance.php - Enhanced version with role-based access

require_once __DIR__ . '/Setting.php';
require_once __DIR__ . '/Employee.php';
=======
// models/Attendance.php
require_once __DIR__ . '/Setting.php';
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335

class Attendance {
    private $conn;
    private $table_name = "attendance_logs";

    // Properties from Database Table
    public $id;
    public $employee_id;
    public $clock_in_time;
    public $clock_out_time;
    public $clock_in_latitude;
    public $clock_in_longitude;
    public $clock_out_latitude;
    public $clock_out_longitude;
    public $clock_in_image_path;
    public $clock_out_image_path;
    public $status;
<<<<<<< HEAD
    public $work_hours;
    public $ot_hours;
=======
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335

    // Properties for handling data from controller
    public $clock_in_image_data;
    public $clock_out_image_data;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Saves a base64 encoded image to a file.
     * @param string $base64_string The base64 encoded image data.
     * @param int $employee_id The ID of the employee.
     * @param string $type The type of clocking ('in' or 'out').
     * @return string|null The path to the saved image or null on failure.
     */
    private function saveBase64Image($base64_string, $employee_id, $type) {
        if (empty($base64_string) || !preg_match('/^data:image\/(\w+);base64,/', $base64_string)) {
            return null;
        }

        list($img_type, $data) = explode(';', $base64_string);
<<<<<<< HEAD
        list(, $data) = explode(',', $data);
        $data = base64_decode($data);

        $upload_dir = __DIR__ . '/../uploads/attendance/';
=======
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);

        // ===== จุดที่แก้ไข: แก้ไขเส้นทางไปยังโฟลเดอร์ uploads =====
        // จาก __DIR__ . '/../../uploads/attendance/'
        // เป็น __DIR__ . '/../uploads/attendance/'
        $upload_dir = __DIR__ . '/../uploads/attendance/';
        
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $filename = $employee_id . '_' . time() . '_' . $type . '.jpg';
        $filepath = $upload_dir . $filename;

        if (file_put_contents($filepath, $data)) {
            return 'uploads/attendance/' . $filename;
        }

        return null;
    }

    /**
     * Creates a new clock-in record.
     * @return bool True on success, false on failure.
     */
<<<<<<< HEAD
=======
    // ===== ฟังก์ชันบันทึกเวลาเข้างาน (แก้ไข) =====
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
    public function createClockIn() {
        if ($this->hasClockedInToday()) {
            return false;
        }

<<<<<<< HEAD
=======
        // --- ส่วนที่เพิ่ม: ตรรกะการคำนวณสถานะมาสาย ---
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
        $setting = new Setting($this->conn);
        $work_start_time_str = $setting->getSettingValue('work_start_time', '08:30');
        $grace_period_minutes = (int)$setting->getSettingValue('grace_period_minutes', 15);

<<<<<<< HEAD
        $clock_in_datetime = new DateTime();
        $allowed_late_datetime = new DateTime(date('Y-m-d') . ' ' . $work_start_time_str);
        $allowed_late_datetime->modify("+{$grace_period_minutes} minutes");

        $this->status = ($clock_in_datetime > $allowed_late_datetime) ? 'สาย' : 'ปกติ';
        
=======
        // สร้าง object เวลาเข้างานจริง
        $clock_in_datetime = new DateTime();
        
        // สร้าง object เวลาเข้างานที่อนุโลมให้สายได้
        $allowed_late_datetime = new DateTime(date('Y-m-d') . ' ' . $work_start_time_str);
        $allowed_late_datetime->modify("+{$grace_period_minutes} minutes");

        // เปรียบเทียบเวลา
        if ($clock_in_datetime > $allowed_late_datetime) {
            $this->status = 'สาย';
        } else {
            $this->status = 'ปกติ';
        }
        // -----------------------------------------

>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
        $image_path = $this->saveBase64Image($this->clock_in_image_data, $this->employee_id, 'in');

        $query = "INSERT INTO " . $this->table_name . "
            SET
                employee_id = :employee_id,
                clock_in_time = NOW(),
                clock_in_latitude = :latitude,
                clock_in_longitude = :longitude,
                clock_in_image_path = :image_path,
<<<<<<< HEAD
                status = :status";
=======
                status = :status"; // ใช้ status ที่คำนวณได้
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":employee_id", $this->employee_id);
        $stmt->bindParam(":latitude", $this->clock_in_latitude);
        $stmt->bindParam(":longitude", $this->clock_in_longitude);
        $stmt->bindParam(":image_path", $image_path);
<<<<<<< HEAD
        $stmt->bindParam(":status", $this->status);
=======
        $stmt->bindParam(":status", $this->status); // Bind status ใหม่
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335

        return $stmt->execute();
    }

<<<<<<< HEAD
    /**
     * Creates a new clock-out record for today.
     * @return bool True on success, false on failure.
     */
    public function createClockOut() {
        $today_log_stmt = $this->getTodayAttendance($this->employee_id);
        if ($today_log_stmt->rowCount() == 0) {
            return false;
=======
    // ===== ฟังก์ชันบันทึกเวลาออกงาน (แก้ไข) =====
    public function createClockOut() {
        // --- ส่วนที่เพิ่ม: ตรรกะการคำนวณชั่วโมงทำงาน ---
        $today_log_stmt = $this->getTodayAttendance($this->employee_id);
        if ($today_log_stmt->rowCount() == 0) {
            return false; // ไม่พบรายการเข้างานของวันนี้
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
        }
        $today_log = $today_log_stmt->fetch(PDO::FETCH_ASSOC);
        
        $clock_in_dt = new DateTime($today_log['clock_in_time']);
<<<<<<< HEAD
        $clock_out_dt = new DateTime();

        $interval = $clock_in_dt->diff($clock_out_dt);
        $work_minutes = ($interval->h * 60) + $interval->i;
        $work_hours = round($work_minutes / 60, 2);

=======
        $clock_out_dt = new DateTime(); // เวลาปัจจุบันคือเวลาออกงาน

        $interval = $clock_in_dt->diff($clock_out_dt);
        $work_minutes = ($interval->h * 60) + $interval->i;
        $work_hours = round($work_minutes / 60, 2); // แปลงเป็นชั่วโมงทศนิยม
        // -----------------------------------------

        // --- ส่วนที่เพิ่ม: ตรรกะการคำนวณ OT ---
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
        $setting = new Setting($this->conn);
        $ot_start_time_str = $setting->getSettingValue('ot_start_time', '18:00');
        $ot_start_datetime = new DateTime(date('Y-m-d') . ' ' . $ot_start_time_str);
        $ot_hours = 0;
        if ($clock_out_dt > $ot_start_datetime) {
            $ot_interval = $clock_out_dt->diff($ot_start_datetime);
            $ot_minutes = ($ot_interval->h * 60) + $ot_interval->i;
            $ot_hours = round($ot_minutes / 60, 2);
        }
<<<<<<< HEAD
=======
        // ------------------------------------
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335

        $image_path = $this->saveBase64Image($this->clock_out_image_data, $this->employee_id, 'out');

        $query = "UPDATE " . $this->table_name . "
            SET
                clock_out_time = NOW(),
                clock_out_latitude = :latitude,
                clock_out_longitude = :longitude,
                clock_out_image_path = :image_path,
<<<<<<< HEAD
                work_hours = :work_hours,
=======
                work_hours = :work_hours, -- เพิ่มการบันทึกชั่วโมงทำงาน
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
                ot_hours = :ot_hours
            WHERE
                id = :id AND clock_out_time IS NULL";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $today_log['id']);
        $stmt->bindParam(":latitude", $this->clock_out_latitude);
        $stmt->bindParam(":longitude", $this->clock_out_longitude);
        $stmt->bindParam(":image_path", $image_path);
        $stmt->bindParam(":work_hours", $work_hours);
        $stmt->bindParam(":ot_hours", $ot_hours);

        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        return false;
    }

    /**
     * Gets today's attendance record for a specific employee.
     * @param int $employee_id The ID of the employee.
     * @return PDOStatement The statement object.
     */
    public function getTodayAttendance($employee_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE employee_id = ? AND DATE(clock_in_time) = CURDATE() 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $employee_id);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Checks if the employee has already clocked in today.
     * @return bool True if clocked in, false otherwise.
     */
    private function hasClockedInToday() {
        $stmt = $this->getTodayAttendance($this->employee_id);
        return $stmt->rowCount() > 0;
    }

    /**
     * Reads the attendance history for a specific employee.
     * @param int $employee_id The ID of the employee.
     * @return PDOStatement The statement object.
     */
    public function readHistoryByEmployee($employee_id) {
<<<<<<< HEAD
        $query = "SELECT 
            al.*,
            e.employee_code,
            e.first_name_th,
            e.last_name_th,
            e.profile_image_path,
            d.name_th as department_name,
            p.name_th as position_name
        FROM " . $this->table_name . " al
        JOIN employees e ON al.employee_id = e.id
        LEFT JOIN departments d ON e.department_id = d.id
        LEFT JOIN positions p ON e.position_id = p.id
        WHERE al.employee_id = ? 
        ORDER BY al.clock_in_time DESC";
=======
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE employee_id = ? 
                  ORDER BY clock_in_time DESC";
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $employee_id);
        $stmt->execute();
        return $stmt;
    }
<<<<<<< HEAD

    /**
     * Gets attendance overview for all employees (Admin/HR only)
     * @param array $filters Array of filters (date_from, date_to, department_id, status)
     * @return PDOStatement
     */
    public function getAttendanceOverview($filters = []) {
        $where_conditions = ["1=1"];
        $params = [];
        
        // Base query with employee join
        $base_query = "SELECT 
            al.*,
            e.employee_code,
            e.first_name_th,
            e.last_name_th,
            e.profile_image_path,
            d.name_th as department_name,
            p.name_th as position_name
        FROM " . $this->table_name . " al
        JOIN employees e ON al.employee_id = e.id
        LEFT JOIN departments d ON e.department_id = d.id
        LEFT JOIN positions p ON e.position_id = p.id";
        
        // Apply filters
        if (!empty($filters['date_from'])) {
            $where_conditions[] = "DATE(al.clock_in_time) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $where_conditions[] = "DATE(al.clock_in_time) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }
        
        if (!empty($filters['department_id'])) {
            $where_conditions[] = "e.department_id = :department_id";
            $params[':department_id'] = $filters['department_id'];
        }
        
        if (!empty($filters['status'])) {
            $where_conditions[] = "al.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['employee_id'])) {
            $where_conditions[] = "al.employee_id = :employee_id";
            $params[':employee_id'] = $filters['employee_id'];
        }
        
        $query = $base_query . " WHERE " . implode(" AND ", $where_conditions) . " ORDER BY al.clock_in_time DESC LIMIT 1000";
        
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt;
    }

    /**
     * Gets attendance statistics for dashboard
     * @param array $filters
     * @return array
     */
    public function getAttendanceStats($filters = []) {
        $where_conditions = ["1=1"];
        $params = [];
        
        // Default to today if no date filter
        if (empty($filters['date_from']) && empty($filters['date_to'])) {
            $where_conditions[] = "DATE(al.clock_in_time) = CURDATE()";
        } else {
            if (!empty($filters['date_from'])) {
                $where_conditions[] = "DATE(al.clock_in_time) >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }
            if (!empty($filters['date_to'])) {
                $where_conditions[] = "DATE(al.clock_in_time) <= :date_to";
                $params[':date_to'] = $filters['date_to'];
            }
        }
        
        if (!empty($filters['department_id'])) {
            $where_conditions[] = "e.department_id = :department_id";
            $params[':department_id'] = $filters['department_id'];
        }
        
        $query = "SELECT 
            COUNT(*) as total_attendance,
            SUM(CASE WHEN al.status = 'ปกติ' THEN 1 ELSE 0 END) as on_time,
            SUM(CASE WHEN al.status = 'สาย' THEN 1 ELSE 0 END) as late,
            SUM(CASE WHEN al.status = 'ขาดงาน' THEN 1 ELSE 0 END) as absent,
            COALESCE(AVG(al.work_hours), 0) as avg_work_hours,
            COALESCE(SUM(al.ot_hours), 0) as total_ot_hours,
            COUNT(DISTINCT al.employee_id) as unique_employees
        FROM " . $this->table_name . " al
        JOIN employees e ON al.employee_id = e.id
        WHERE " . implode(" AND ", $where_conditions);
        
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Gets departments list for filter dropdown
     * @return PDOStatement
     */
    public function getDepartments() {
        $query = "SELECT id, name_th FROM departments ORDER BY name_th";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Gets employees list for filter dropdown
     * @param int|null $department_id
     * @return PDOStatement
     */
    public function getEmployees($department_id = null) {
        $where = $department_id ? "WHERE e.department_id = :department_id AND" : "WHERE";
        $query = "SELECT 
            e.id, 
            e.employee_code,
            CONCAT(e.first_name_th, ' ', e.last_name_th) as full_name,
            d.name_th as department_name
        FROM employees e 
        LEFT JOIN departments d ON e.department_id = d.id
        $where e.status = 'ทำงาน' 
        ORDER BY e.first_name_th, e.last_name_th";
        
        $stmt = $this->conn->prepare($query);
        if ($department_id) {
            $stmt->bindParam(':department_id', $department_id);
        }
        $stmt->execute();
        return $stmt;
    }

    /**
     * Export attendance data to CSV format
     * @param array $filters
     * @return array
     */
    public function exportAttendanceData($filters = []) {
        $stmt = $this->getAttendanceOverview($filters);
        $data = [];
        
        // Header row
        $data[] = [
            'วันที่',
            'รหัสพนักงาน', 
            'ชื่อ-นามสกุล',
            'แผนก',
            'ตำแหน่ง',
            'เวลาเข้า',
            'เวลาออก',
            'ชั่วโมงทำงาน',
            'ชั่วโมง OT',
            'สถานะ'
        ];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = [
                date('d/m/Y', strtotime($row['clock_in_time'])),
                $row['employee_code'],
                $row['first_name_th'] . ' ' . $row['last_name_th'],
                $row['department_name'] ?? '-',
                $row['position_name'] ?? '-',
                date('H:i:s', strtotime($row['clock_in_time'])),
                $row['clock_out_time'] ? date('H:i:s', strtotime($row['clock_out_time'])) : '-',
                $row['work_hours'] ? number_format($row['work_hours'], 2) : '-',
                $row['ot_hours'] ? number_format($row['ot_hours'], 2) : '-',
                $row['status']
            ];
        }
        
        return $data;
    }

    /**
     * Get daily attendance summary for charts
     * @param array $filters
     * @return PDOStatement
     */
    public function getDailyAttendanceSummary($filters = []) {
        $where_conditions = ["1=1"];
        $params = [];
        
        // Default to last 30 days
        if (empty($filters['date_from']) && empty($filters['date_to'])) {
            $where_conditions[] = "DATE(al.clock_in_time) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        } else {
            if (!empty($filters['date_from'])) {
                $where_conditions[] = "DATE(al.clock_in_time) >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }
            if (!empty($filters['date_to'])) {
                $where_conditions[] = "DATE(al.clock_in_time) <= :date_to";
                $params[':date_to'] = $filters['date_to'];
            }
        }
        
        if (!empty($filters['department_id'])) {
            $where_conditions[] = "e.department_id = :department_id";
            $params[':department_id'] = $filters['department_id'];
        }
        
        $query = "SELECT 
            DATE(al.clock_in_time) as attendance_date,
            COUNT(*) as total_count,
            SUM(CASE WHEN al.status = 'ปกติ' THEN 1 ELSE 0 END) as on_time_count,
            SUM(CASE WHEN al.status = 'สาย' THEN 1 ELSE 0 END) as late_count,
            SUM(CASE WHEN al.status = 'ขาดงาน' THEN 1 ELSE 0 END) as absent_count
        FROM " . $this->table_name . " al
        JOIN employees e ON al.employee_id = e.id
        WHERE " . implode(" AND ", $where_conditions) . "
        GROUP BY DATE(al.clock_in_time)
        ORDER BY attendance_date DESC
        LIMIT 30";
        
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt;
    }

    /**
     * Device integration: Log attendance from external device
     * @param string $employee_code
     * @param string $timestamp
     * @return array
     */
    public function logFromDevice($employee_code, $timestamp) {
        // 1. ค้นหา employee_id จาก employee_code
        $employee_model = new Employee($this->conn);
        $employee_model->employee_code = $employee_code;
        $employee = $employee_model->readOneByEmployeeCode();

        if (!$employee || !$employee->id) {
            return ['status' => false, 'message' => 'Employee code not found.', 'action' => 'none'];
        }
        $employee_id = $employee->id;

        // 2. ตรวจสอบว่าวันนี้มีการลงเวลาเข้างานแล้วหรือยัง
        $date = date('Y-m-d', strtotime($timestamp));
        $query_check = "SELECT * FROM " . $this->table_name . " WHERE employee_id = ? AND DATE(clock_in_time) = ? LIMIT 1";
        $stmt_check = $this->conn->prepare($query_check);
        $stmt_check->execute([$employee_id, $date]);
        $existing_log = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($existing_log) {
            // ถ้ามี log ของวันนี้แล้ว -> ถือเป็นการลงเวลาออก
            if (empty($existing_log['clock_out_time'])) {
                // คำนวณ work_hours และ ot_hours
                $clock_in_dt = new DateTime($existing_log['clock_in_time']);
                $clock_out_dt = new DateTime($timestamp);
                $interval = $clock_in_dt->diff($clock_out_dt);
                $work_hours = $interval->h + ($interval->i / 60);

                // คำนวณ OT
                $setting = new Setting($this->conn);
                $ot_start_time_str = $setting->getSettingValue('ot_start_time', '18:00');
                $ot_start_datetime = new DateTime($date . ' ' . $ot_start_time_str);
                $ot_hours = 0;
                if ($clock_out_dt > $ot_start_datetime) {
                    $ot_interval = $clock_out_dt->diff($ot_start_datetime);
                    $ot_minutes = ($ot_interval->h * 60) + $ot_interval->i;
                    $ot_hours = round($ot_minutes / 60, 2);
                }

                $query_update = "UPDATE " . $this->table_name . " 
                               SET clock_out_time = :timestamp, work_hours = :work_hours, ot_hours = :ot_hours 
                               WHERE id = :id";
                $stmt = $this->conn->prepare($query_update);
                $stmt->bindParam(':timestamp', $timestamp);
                $stmt->bindParam(':work_hours', $work_hours);
                $stmt->bindParam(':ot_hours', $ot_hours);
                $stmt->bindParam(':id', $existing_log['id']);
                
                if ($stmt->execute()) {
                    return ['status' => true, 'message' => 'Clock-out recorded.', 'action' => 'clock_out'];
                }
            }
            return ['status' => false, 'message' => 'Already clocked out for today.', 'action' => 'none'];
        } else {
            // ถ้ายังไม่มี log ของวันนี้ -> ถือเป็นการลงเวลาเข้า
            // ตรวจสอบการมาสาย
            $setting = new Setting($this->conn);
            $work_start_time_str = $setting->getSettingValue('work_start_time', '08:30');
            $grace_period_minutes = (int)$setting->getSettingValue('grace_period_minutes', 15);

            $clock_in_datetime = new DateTime($timestamp);
            $allowed_late_datetime = new DateTime($date . ' ' . $work_start_time_str);
            $allowed_late_datetime->modify("+{$grace_period_minutes} minutes");

            $status = ($clock_in_datetime > $allowed_late_datetime) ? 'สาย' : 'ปกติ';

            $query_insert = "INSERT INTO " . $this->table_name . " (employee_id, clock_in_time, status) VALUES (:employee_id, :timestamp, :status)";
            $stmt = $this->conn->prepare($query_insert);
            $stmt->bindParam(':employee_id', $employee_id);
            $stmt->bindParam(':timestamp', $timestamp);
            $stmt->bindParam(':status', $status);

            if ($stmt->execute()) {
                return ['status' => true, 'message' => 'Clock-in recorded.', 'action' => 'clock_in', 'status_type' => $status];
            }
        }
        
        return ['status' => false, 'message' => 'Database error.', 'action' => 'none'];
    }
}
?>
=======
}
?>
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
