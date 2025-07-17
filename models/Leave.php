<?php
// models/Leave.php

class Leave
{
    private $conn;
    private $leave_requests_table = "leave_requests";
    private $leave_types_table = "leave_types";

    // Properties
    public $id, $employee_id, $leave_type_id, $start_date, $end_date, $reason, $status, $attachment_path;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // ดึงข้อมูลประเภทการลาทั้งหมด
    public function getLeaveTypes()
    {
        $query = "SELECT * FROM " . $this->leave_types_table . " ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // สร้างคำขอการลาใหม่
    public function createLeaveRequest() {
        // ===== จุดที่แก้ไข: เพิ่มฟิลด์ attachment_path ใน query =====
        $query = "INSERT INTO " . $this->leave_requests_table . "
            SET
                employee_id = :employee_id,
                leave_type_id = :leave_type_id,
                start_date = :start_date,
                end_date = :end_date,
                reason = :reason,
                attachment_path = :attachment_path,
                status = 'รออนุมัติ'";

        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->employee_id = htmlspecialchars(strip_tags($this->employee_id));
        $this->leave_type_id = htmlspecialchars(strip_tags($this->leave_type_id));
        $this->start_date = htmlspecialchars(strip_tags($this->start_date));
        $this->end_date = htmlspecialchars(strip_tags($this->end_date));
        $this->reason = htmlspecialchars(strip_tags($this->reason));
        $this->attachment_path = htmlspecialchars(strip_tags($this->attachment_path));

        // Bind data
        $stmt->bindParam(":employee_id", $this->employee_id);
        $stmt->bindParam(":leave_type_id", $this->leave_type_id);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":reason", $this->reason);
        // ===== จุดที่แก้ไข: Bind parameter ใหม่ =====
        $stmt->bindParam(":attachment_path", $this->attachment_path);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // เพิ่มฟังก์ชัน readHistoryByEmployee()
    public function readHistoryByEmployee($employee_id)
    {
        $query = "SELECT lr.*, lt.name as leave_type_name 
              FROM " . $this->leave_requests_table . " lr
              LEFT JOIN " . $this->leave_types_table . " lt ON lr.leave_type_id = lt.id
              WHERE lr.employee_id = ? 
              ORDER BY lr.start_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $employee_id);
        $stmt->execute();
        return $stmt;
    }

    // ===== ฟังก์ชันใหม่ที่เพิ่มเข้ามา: ดึงข้อมูลใบลา 1 รายการ =====
    public function readOne() {
        $query = "SELECT * FROM " . $this->leave_requests_table . " WHERE id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->employee_id = $row['employee_id'];
            $this->leave_type_id = $row['leave_type_id'];
            $this->start_date = $row['start_date'];
            $this->end_date = $row['end_date'];
            $this->reason = $row['reason'];
            $this->status = $row['status'];
            $this->attachment_path = $row['attachment_path'];
            
            // คืนค่าข้อมูลแถวเพื่อให้ Controller นำไปใช้ต่อได้
            return $row;
        }

        return null;
    }

    // ===== ฟังก์ชันใหม่: ดึงคำขอที่รออนุมัติโดยหัวหน้างาน =====
    public function getPendingRequestsBySupervisor($supervisor_id) {
        $query = "SELECT 
                    lr.id, 
                    lr.start_date, 
                    lr.end_date, 
                    lr.reason,
                    lt.name as leave_type_name,
                    CONCAT(e.prefix, e.first_name_th, ' ', e.last_name_th) as employee_name
                  FROM 
                    " . $this->leave_requests_table . " lr
                    JOIN employees e ON lr.employee_id = e.id
                    JOIN leave_types lt ON lr.leave_type_id = lt.id
                  WHERE 
                    e.supervisor_id = ? AND lr.status = 'รออนุมัติ'
                  ORDER BY 
                    lr.created_at ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $supervisor_id);
        $stmt->execute();
        return $stmt;
    }

    // ===== ฟังก์ชันใหม่: อัปเดตสถานะการลา =====
    public function updateStatus($status) {
        $query = "UPDATE " . $this->leave_requests_table . " SET status = :status WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind data
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $this->id);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

        // ===== ฟังก์ชันใหม่: คำนวณยอดวันลาคงเหลือ =====
    public function getLeaveBalances($employee_id) {
        $balances = [];
        $current_year = date('Y');

        // 1. Get all leave types
        $leave_types_stmt = $this->getLeaveTypes();
        
        while ($type = $leave_types_stmt->fetch(PDO::FETCH_ASSOC)) {
            // 2. For each type, calculate used days for the current year
            $query = "SELECT SUM(DATEDIFF(DATE(end_date), DATE(start_date)) + 1) as days_used 
                      FROM " . $this->leave_requests_table . "
                      WHERE employee_id = :employee_id 
                        AND leave_type_id = :leave_type_id
                        AND status = 'อนุมัติ'
                        AND YEAR(start_date) = :year";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':employee_id', $employee_id);
            $stmt->bindParam(':leave_type_id', $type['id']);
            $stmt->bindParam(':year', $current_year);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $days_used = $result['days_used'] ?? 0;
            
            $balances[] = [
                'name' => $type['name'],
                'max_days' => $type['max_days_per_year'],
                'days_used' => (int)$days_used,
                'remaining' => $type['max_days_per_year'] - (int)$days_used,
                'icon' => $this->getLeaveIcon($type['name']) // Helper for icon
            ];
        }
        
        return $balances;
    }

    // Helper function to get an icon based on leave type name
    private function getLeaveIcon($leave_name) {
        if (strpos($leave_name, 'ป่วย') !== false) return 'fas fa-pills text-blue-500';
        if (strpos($leave_name, 'กิจ') !== false) return 'fas fa-briefcase text-green-500';
        if (strpos($leave_name, 'พักผ่อน') !== false) return 'fas fa-umbrella-beach text-yellow-500';
        if (strpos($leave_name, 'คลอด') !== false) return 'fas fa-baby text-pink-500';
        if (strpos($leave_name, 'อุปสมบท') !== false) return 'fas fa-praying-hands text-orange-500';
        return 'fas fa-calendar-alt text-gray-500';
    }
}
?>