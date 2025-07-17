<?php
// models/Attendance.php
require_once __DIR__ . '/Setting.php';

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
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);

        // ===== จุดที่แก้ไข: แก้ไขเส้นทางไปยังโฟลเดอร์ uploads =====
        // จาก __DIR__ . '/../../uploads/attendance/'
        // เป็น __DIR__ . '/../uploads/attendance/'
        $upload_dir = __DIR__ . '/../uploads/attendance/';
        
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
    // ===== ฟังก์ชันบันทึกเวลาเข้างาน (แก้ไข) =====
    public function createClockIn() {
        if ($this->hasClockedInToday()) {
            return false;
        }

        // --- ส่วนที่เพิ่ม: ตรรกะการคำนวณสถานะมาสาย ---
        $setting = new Setting($this->conn);
        $work_start_time_str = $setting->getSettingValue('work_start_time', '08:30');
        $grace_period_minutes = (int)$setting->getSettingValue('grace_period_minutes', 15);

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

        $image_path = $this->saveBase64Image($this->clock_in_image_data, $this->employee_id, 'in');

        $query = "INSERT INTO " . $this->table_name . "
            SET
                employee_id = :employee_id,
                clock_in_time = NOW(),
                clock_in_latitude = :latitude,
                clock_in_longitude = :longitude,
                clock_in_image_path = :image_path,
                status = :status"; // ใช้ status ที่คำนวณได้

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":employee_id", $this->employee_id);
        $stmt->bindParam(":latitude", $this->clock_in_latitude);
        $stmt->bindParam(":longitude", $this->clock_in_longitude);
        $stmt->bindParam(":image_path", $image_path);
        $stmt->bindParam(":status", $this->status); // Bind status ใหม่

        return $stmt->execute();
    }

    // ===== ฟังก์ชันบันทึกเวลาออกงาน (แก้ไข) =====
    public function createClockOut() {
        // --- ส่วนที่เพิ่ม: ตรรกะการคำนวณชั่วโมงทำงาน ---
        $today_log_stmt = $this->getTodayAttendance($this->employee_id);
        if ($today_log_stmt->rowCount() == 0) {
            return false; // ไม่พบรายการเข้างานของวันนี้
        }
        $today_log = $today_log_stmt->fetch(PDO::FETCH_ASSOC);
        
        $clock_in_dt = new DateTime($today_log['clock_in_time']);
        $clock_out_dt = new DateTime(); // เวลาปัจจุบันคือเวลาออกงาน

        $interval = $clock_in_dt->diff($clock_out_dt);
        $work_minutes = ($interval->h * 60) + $interval->i;
        $work_hours = round($work_minutes / 60, 2); // แปลงเป็นชั่วโมงทศนิยม
        // -----------------------------------------

        // --- ส่วนที่เพิ่ม: ตรรกะการคำนวณ OT ---
        $setting = new Setting($this->conn);
        $ot_start_time_str = $setting->getSettingValue('ot_start_time', '18:00');
        $ot_start_datetime = new DateTime(date('Y-m-d') . ' ' . $ot_start_time_str);
        $ot_hours = 0;
        if ($clock_out_dt > $ot_start_datetime) {
            $ot_interval = $clock_out_dt->diff($ot_start_datetime);
            $ot_minutes = ($ot_interval->h * 60) + $ot_interval->i;
            $ot_hours = round($ot_minutes / 60, 2);
        }
        // ------------------------------------

        $image_path = $this->saveBase64Image($this->clock_out_image_data, $this->employee_id, 'out');

        $query = "UPDATE " . $this->table_name . "
            SET
                clock_out_time = NOW(),
                clock_out_latitude = :latitude,
                clock_out_longitude = :longitude,
                clock_out_image_path = :image_path,
                work_hours = :work_hours, -- เพิ่มการบันทึกชั่วโมงทำงาน
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
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE employee_id = ? 
                  ORDER BY clock_in_time DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $employee_id);
        $stmt->execute();
        return $stmt;
    }
}
?>
