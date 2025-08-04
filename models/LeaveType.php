<?php
// models/LeaveType.php

class LeaveType 
{
    private $conn;
    private $table_name = "leave_types";
    
    public $id;
    public $name;
    public $max_days_per_year;
    public $is_paid;
    public $created_at;
    public $updated_at;
    
    public function __construct($db) 
    {
        $this->conn = $db;
    }
    
    /**
     * Get all leave types
     * @return PDOStatement
     */
    public function readAll() 
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * Get single leave type by ID
     * @param int $id
     * @return bool
     */
    public function readOne($id) 
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->max_days_per_year = $row['max_days_per_year'];
            $this->is_paid = $row['is_paid'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }
    
    /**
     * Create new leave type
     * @return bool
     */
    public function create() 
    {
        $query = "INSERT INTO " . $this->table_name . " 
                 (name, max_days_per_year, is_paid) 
                 VALUES (:name, :max_days_per_year, :is_paid)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize input
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->max_days_per_year = (int)$this->max_days_per_year;
        $this->is_paid = (int)$this->is_paid;
        
        // Bind values
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':max_days_per_year', $this->max_days_per_year);
        $stmt->bindParam(':is_paid', $this->is_paid);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }
    
    /**
     * Update leave type
     * @return bool
     */
    public function update() 
    {
        $query = "UPDATE " . $this->table_name . " 
                 SET name = :name, 
                     max_days_per_year = :max_days_per_year, 
                     is_paid = :is_paid,
                     updated_at = CURRENT_TIMESTAMP
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize input
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->max_days_per_year = (int)$this->max_days_per_year;
        $this->is_paid = (int)$this->is_paid;
        $this->id = (int)$this->id;
        
        // Bind values
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':max_days_per_year', $this->max_days_per_year);
        $stmt->bindParam(':is_paid', $this->is_paid);
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }
    
    /**
     * Delete leave type
     * @return bool
     */
    public function delete() 
    {
        // Check if leave type is being used in any leave requests
        $check_query = "SELECT COUNT(*) as count FROM leave_requests WHERE leave_type_id = :id";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(':id', $this->id);
        $check_stmt->execute();
        $result = $check_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            throw new Exception('ไม่สามารถลบประเภทการลานี้ได้ เนื่องจากมีการใช้งานในระบบแล้ว');
        }
        
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->id = (int)$this->id;
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }
    
    /**
     * Check if leave type name already exists
     * @param string $name
     * @param int $exclude_id (for update operations)
     * @return bool
     */
    public function nameExists($name, $exclude_id = null) 
    {
        $query = "SELECT id FROM " . $this->table_name . " WHERE name = :name";
        
        if ($exclude_id !== null) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        
        if ($exclude_id !== null) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Get active leave types (for dropdown/select options)
     * @return array
     */
    public function getActiveLeaveTypes() 
    {
        $query = "SELECT id, name, max_days_per_year, is_paid 
                 FROM " . $this->table_name . " 
                 ORDER BY name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $leave_types = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $leave_types[] = $row;
        }
        
        return $leave_types;
    }
    
    /**
     * Get leave type statistics
     * @return array
     */
    public function getStats() 
    {
        $stats = [];
        
        // Total leave types
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Paid leave types
        $query = "SELECT COUNT(*) as paid FROM " . $this->table_name . " WHERE is_paid = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['paid'] = $stmt->fetch(PDO::FETCH_ASSOC)['paid'];
        
        // Unpaid leave types
        $stats['unpaid'] = $stats['total'] - $stats['paid'];
        
        // Most used leave type (if leave_requests table exists)
        try {
            $query = "SELECT lt.name, COUNT(lr.id) as usage_count 
                     FROM " . $this->table_name . " lt
                     LEFT JOIN leave_requests lr ON lt.id = lr.leave_type_id
                     GROUP BY lt.id, lt.name
                     ORDER BY usage_count DESC
                     LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $most_used = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['most_used'] = $most_used ? $most_used['name'] : 'ไม่มีข้อมูล';
        } catch (Exception $e) {
            $stats['most_used'] = 'ไม่มีข้อมูล';
        }
        
        return $stats;
    }
    
    /**
     * Validate leave type data
     * @return array Array of validation errors
     */
    public function validate() 
    {
        $errors = [];
        
        // Validate name
        if (empty(trim($this->name))) {
            $errors[] = 'กรุณากรอกชื่อประเภทการลา';
        } elseif (strlen(trim($this->name)) > 100) {
            $errors[] = 'ชื่อประเภทการลาต้องไม่เกิน 100 ตัวอักษร';
        } else {
            // Check if name already exists
            $exclude_id = isset($this->id) ? $this->id : null;
            if ($this->nameExists(trim($this->name), $exclude_id)) {
                $errors[] = 'ชื่อประเภทการลานี้มีอยู่ในระบบแล้ว';
            }
        }
        
        // Validate max_days_per_year
        if (!is_numeric($this->max_days_per_year) || $this->max_days_per_year < 0) {
            $errors[] = 'จำนวนวันลาสูงสุดต่อปีต้องเป็นตัวเลขที่มากกว่าหรือเท่ากับ 0';
        } elseif ($this->max_days_per_year > 365) {
            $errors[] = 'จำนวนวันลาสูงสุดต่อปีต้องไม่เกิน 365 วัน';
        }
        
        // Validate is_paid
        if (!in_array($this->is_paid, [0, 1])) {
            $errors[] = 'สถานะการจ่ายเงินไม่ถูกต้อง';
        }
        
        return $errors;
    }
    
    /**
     * Get formatted display text
     * @return string
     */
    public function getDisplayText() 
    {
        $paid_text = $this->is_paid ? 'ได้เงิน' : 'ไม่ได้เงิน';
        $max_days_text = $this->max_days_per_year > 0 ? $this->max_days_per_year . ' วัน/ปี' : 'ไม่จำกัด';
        
        return $this->name . ' (' . $paid_text . ', ' . $max_days_text . ')';
    }
    
    /**
     * Export data for reports
     * @return array
     */
    public function exportData() 
    {
        $query = "SELECT 
                    name as 'ชื่อประเภทการลา',
                    max_days_per_year as 'วันลาสูงสุด/ปี',
                    CASE WHEN is_paid = 1 THEN 'ได้เงิน' ELSE 'ไม่ได้เงิน' END as 'สถานะการจ่ายเงิน',
                    DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') as 'วันที่สร้าง',
                    DATE_FORMAT(updated_at, '%d/%m/%Y %H:%i') as 'วันที่แก้ไขล่าสุด'
                  FROM " . $this->table_name . " 
                  ORDER BY name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>