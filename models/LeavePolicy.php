<?php
// models/LeavePolicy.php

class LeavePolicy 
{
    private $conn;
    private $table_name = "leave_policies";
    
    public $id;
    public $leave_type_id;
    public $days_allowed_per_year;
    public $is_unlimited;
    public $can_be_carried_over;
    public $max_carry_over_days;
    public $min_notice_days;
    public $max_consecutive_days;
    public $requires_approval;
    public $description;
    public $created_at;
    public $updated_at;

    public function __construct($db) 
    {
        $this->conn = $db;
    }

    /**
     * Get all leave policies with leave type information
     * @return PDOStatement
     */
    public function readAll() 
    {
        $query = "SELECT 
                    lp.*, 
                    lt.name as leave_type_name,
                    lt.is_paid
                  FROM " . $this->table_name . " lp
                  LEFT JOIN leave_types lt ON lp.leave_type_id = lt.id
                  ORDER BY lt.name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Get single leave policy by ID
     * @param int $id
     * @return bool
     */
    public function readOne($id) 
    {
        $query = "SELECT 
                    lp.*, 
                    lt.name as leave_type_name,
                    lt.is_paid
                  FROM " . $this->table_name . " lp
                  LEFT JOIN leave_types lt ON lp.leave_type_id = lt.id
                  WHERE lp.id = :id 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->leave_type_id = $row['leave_type_id'];
            $this->days_allowed_per_year = $row['days_allowed_per_year'];
            $this->is_unlimited = $row['is_unlimited'];
            $this->can_be_carried_over = $row['can_be_carried_over'];
            $this->max_carry_over_days = $row['max_carry_over_days'];
            $this->min_notice_days = $row['min_notice_days'];
            $this->max_consecutive_days = $row['max_consecutive_days'];
            $this->requires_approval = $row['requires_approval'];
            $this->description = $row['description'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    /**
     * Create new leave policy
     * @return bool
     */
    public function create() 
    {
        $query = "INSERT INTO " . $this->table_name . " 
                 (leave_type_id, days_allowed_per_year, is_unlimited, can_be_carried_over, 
                  max_carry_over_days, min_notice_days, max_consecutive_days, requires_approval, description) 
                 VALUES 
                 (:leave_type_id, :days_allowed_per_year, :is_unlimited, :can_be_carried_over, 
                  :max_carry_over_days, :min_notice_days, :max_consecutive_days, :requires_approval, :description)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize input
        $this->leave_type_id = (int)$this->leave_type_id;
        $this->days_allowed_per_year = (int)$this->days_allowed_per_year;
        $this->is_unlimited = (int)$this->is_unlimited;
        $this->can_be_carried_over = (int)$this->can_be_carried_over;
        $this->max_carry_over_days = (int)$this->max_carry_over_days;
        $this->min_notice_days = (int)$this->min_notice_days;
        $this->max_consecutive_days = (int)$this->max_consecutive_days;
        $this->requires_approval = (int)$this->requires_approval;
        $this->description = htmlspecialchars(strip_tags($this->description));
        
        // Bind values
        $stmt->bindParam(':leave_type_id', $this->leave_type_id);
        $stmt->bindParam(':days_allowed_per_year', $this->days_allowed_per_year);
        $stmt->bindParam(':is_unlimited', $this->is_unlimited);
        $stmt->bindParam(':can_be_carried_over', $this->can_be_carried_over);
        $stmt->bindParam(':max_carry_over_days', $this->max_carry_over_days);
        $stmt->bindParam(':min_notice_days', $this->min_notice_days);
        $stmt->bindParam(':max_consecutive_days', $this->max_consecutive_days);
        $stmt->bindParam(':requires_approval', $this->requires_approval);
        $stmt->bindParam(':description', $this->description);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Update leave policy
     * @return bool
     */
    public function update() 
    {
        $query = "UPDATE " . $this->table_name . " 
                 SET 
                    leave_type_id = :leave_type_id,
                    days_allowed_per_year = :days_allowed_per_year,
                    is_unlimited = :is_unlimited,
                    can_be_carried_over = :can_be_carried_over,
                    max_carry_over_days = :max_carry_over_days,
                    min_notice_days = :min_notice_days,
                    max_consecutive_days = :max_consecutive_days,
                    requires_approval = :requires_approval,
                    description = :description,
                    updated_at = CURRENT_TIMESTAMP
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize input
        $this->leave_type_id = (int)$this->leave_type_id;
        $this->days_allowed_per_year = (int)$this->days_allowed_per_year;
        $this->is_unlimited = (int)$this->is_unlimited;
        $this->can_be_carried_over = (int)$this->can_be_carried_over;
        $this->max_carry_over_days = (int)$this->max_carry_over_days;
        $this->min_notice_days = (int)$this->min_notice_days;
        $this->max_consecutive_days = (int)$this->max_consecutive_days;
        $this->requires_approval = (int)$this->requires_approval;
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->id = (int)$this->id;
        
        // Bind values
        $stmt->bindParam(':leave_type_id', $this->leave_type_id);
        $stmt->bindParam(':days_allowed_per_year', $this->days_allowed_per_year);
        $stmt->bindParam(':is_unlimited', $this->is_unlimited);
        $stmt->bindParam(':can_be_carried_over', $this->can_be_carried_over);
        $stmt->bindParam(':max_carry_over_days', $this->max_carry_over_days);
        $stmt->bindParam(':min_notice_days', $this->min_notice_days);
        $stmt->bindParam(':max_consecutive_days', $this->max_consecutive_days);
        $stmt->bindParam(':requires_approval', $this->requires_approval);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }

    /**
     * Update leave policy by leave_type_id (for bulk updates)
     * @param int $leave_type_id
     * @param array $data
     * @return bool
     */
    public function updateByLeaveType($leave_type_id, $data) 
    {
        $query = "UPDATE " . $this->table_name . " SET
                    days_allowed_per_year = :days_allowed,
                    is_unlimited = :is_unlimited,
                    can_be_carried_over = :can_be_carried_over,
                    max_carry_over_days = :max_carry_over,
                    updated_at = CURRENT_TIMESTAMP
                  WHERE leave_type_id = :leave_type_id";
        
        $stmt = $this->conn->prepare($query);

        // Bind data
        $stmt->bindParam(':days_allowed', $data['days_allowed_per_year']);
        $stmt->bindParam(':is_unlimited', $data['is_unlimited']);
        $stmt->bindParam(':can_be_carried_over', $data['can_be_carried_over']);
        $stmt->bindParam(':max_carry_over', $data['max_carry_over_days']);
        $stmt->bindParam(':leave_type_id', $leave_type_id);

        return $stmt->execute();
    }

    /**
     * Delete leave policy
     * @return bool
     */
    public function delete() 
    {
        // Check if policy is being used in any leave requests
        $check_query = "SELECT COUNT(*) as count FROM leave_requests WHERE leave_type_id = :leave_type_id";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(':leave_type_id', $this->leave_type_id);
        $check_stmt->execute();
        $result = $check_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            throw new Exception('ไม่สามารถลบนโยบายการลานี้ได้ เนื่องจากมีการใช้งานในระบบแล้ว');
        }
        
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->id = (int)$this->id;
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }

    /**
     * Check if leave type already has a policy
     * @param int $leave_type_id
     * @param int $exclude_id
     * @return bool
     */
    public function leaveTypeHasPolicy($leave_type_id, $exclude_id = null) 
    {
        $query = "SELECT id FROM " . $this->table_name . " WHERE leave_type_id = :leave_type_id";
        
        if ($exclude_id !== null) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':leave_type_id', $leave_type_id);
        
        if ($exclude_id !== null) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Get available leave types for dropdown
     * @param int $exclude_policy_id
     * @return array
     */
    public function getAvailableLeaveTypes($exclude_policy_id = null) 
    {
        $query = "SELECT lt.id, lt.name, lt.is_paid 
                 FROM leave_types lt 
                 WHERE lt.id NOT IN (
                     SELECT leave_type_id FROM " . $this->table_name . " 
                     WHERE leave_type_id IS NOT NULL";
        
        if ($exclude_policy_id !== null) {
            $query .= " AND id != :exclude_id";
        }
        
        $query .= ") ORDER BY lt.name ASC";
        
        $stmt = $this->conn->prepare($query);
        
        if ($exclude_policy_id !== null) {
            $stmt->bindParam(':exclude_id', $exclude_policy_id);
        }
        
        $stmt->execute();
        
        $leave_types = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $leave_types[] = $row;
        }
        
        return $leave_types;
    }

    /**
     * Get all leave types (for edit form)
     * @return array
     */
    public function getAllLeaveTypes() 
    {
        $query = "SELECT id, name, is_paid FROM leave_types ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $leave_types = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $leave_types[] = $row;
        }
        
        return $leave_types;
    }

    /**
     * Get policy statistics
     * @return array
     */
    public function getStats() 
    {
        $stats = [];
        
        // Total policies
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Unlimited policies
        $query = "SELECT COUNT(*) as unlimited FROM " . $this->table_name . " WHERE is_unlimited = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['unlimited'] = $stmt->fetch(PDO::FETCH_ASSOC)['unlimited'];
        
        // Policies with carry over
        $query = "SELECT COUNT(*) as carry_over FROM " . $this->table_name . " WHERE can_be_carried_over = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['carry_over'] = $stmt->fetch(PDO::FETCH_ASSOC)['carry_over'];
        
        // Policies requiring approval
        $query = "SELECT COUNT(*) as requires_approval FROM " . $this->table_name . " WHERE requires_approval = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['requires_approval'] = $stmt->fetch(PDO::FETCH_ASSOC)['requires_approval'];
        
        return $stats;
    }

    /**
     * Validate policy data
     * @return array Array of validation errors
     */
    public function validate() 
    {
        $errors = [];
        
        // Validate leave_type_id
        if (empty($this->leave_type_id)) {
            $errors[] = 'กรุณาเลือกประเภทการลา';
        } else {
            // Check if leave type already has a policy
            $exclude_id = isset($this->id) ? $this->id : null;
            if ($this->leaveTypeHasPolicy($this->leave_type_id, $exclude_id)) {
                $errors[] = 'ประเภทการลานี้มีนโยบายอยู่แล้ว';
            }
        }
        
        // Validate days_allowed_per_year
        if (!$this->is_unlimited) {
            if (!is_numeric($this->days_allowed_per_year) || $this->days_allowed_per_year < 0) {
                $errors[] = 'จำนวนวันลาต่อปีต้องเป็นตัวเลขที่มากกว่าหรือเท่ากับ 0';
            } elseif ($this->days_allowed_per_year > 365) {
                $errors[] = 'จำนวนวันลาต่อปีต้องไม่เกิน 365 วัน';
            }
        }
        
        // Validate carry over days
        if ($this->can_be_carried_over) {
            if (!is_numeric($this->max_carry_over_days) || $this->max_carry_over_days < 0) {
                $errors[] = 'จำนวนวันลาสูงสุดที่โอนได้ต้องเป็นตัวเลขที่มากกว่าหรือเท่ากับ 0';
            } elseif ($this->max_carry_over_days > 365) {
                $errors[] = 'จำนวนวันลาสูงสุดที่โอนได้ต้องไม่เกิน 365 วัน';
            }
        }
        
        // Validate notice days
        if (!is_numeric($this->min_notice_days) || $this->min_notice_days < 0) {
            $errors[] = 'จำนวนวันแจ้งล่วงหน้าต้องเป็นตัวเลขที่มากกว่าหรือเท่ากับ 0';
        } elseif ($this->min_notice_days > 365) {
            $errors[] = 'จำนวนวันแจ้งล่วงหน้าต้องไม่เกิน 365 วัน';
        }
        
        // Validate consecutive days
        if (!is_numeric($this->max_consecutive_days) || $this->max_consecutive_days < 0) {
            $errors[] = 'จำนวนวันลาติดต่อกันสูงสุดต้องเป็นตัวเลขที่มากกว่าหรือเท่ากับ 0';
        } elseif ($this->max_consecutive_days > 365) {
            $errors[] = 'จำนวนวันลาติดต่อกันสูงสุดต้องไม่เกิน 365 วัน';
        }
        
        // Validate description length
        if (strlen($this->description) > 1000) {
            $errors[] = 'รายละเอียดต้องไม่เกิน 1000 ตัวอักษร';
        }
        
        return $errors;
    }

    /**
     * Export data for reports
     * @return array
     */
    public function exportData() 
    {
        $query = "SELECT 
                    lt.name as 'ประเภทการลา',
                    CASE WHEN lp.is_unlimited = 1 THEN 'ไม่จำกัด' ELSE lp.days_allowed_per_year END as 'วันลาต่อปี',
                    CASE WHEN lp.can_be_carried_over = 1 THEN 'ได้' ELSE 'ไม่ได้' END as 'โอนวันลาได้',
                    lp.max_carry_over_days as 'วันลาสูงสุดที่โอนได้',
                    lp.min_notice_days as 'แจ้งล่วงหน้า (วัน)',
                    lp.max_consecutive_days as 'ลาติดต่อกันสูงสุด (วัน)',
                    CASE WHEN lp.requires_approval = 1 THEN 'ต้องอนุมัติ' ELSE 'ไม่ต้องอนุมัติ' END as 'การอนุมัติ',
                    DATE_FORMAT(lp.created_at, '%d/%m/%Y %H:%i') as 'วันที่สร้าง',
                    DATE_FORMAT(lp.updated_at, '%d/%m/%Y %H:%i') as 'วันที่แก้ไขล่าสุด'
                  FROM " . $this->table_name . " lp
                  LEFT JOIN leave_types lt ON lp.leave_type_id = lt.id
                  ORDER BY lt.name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>