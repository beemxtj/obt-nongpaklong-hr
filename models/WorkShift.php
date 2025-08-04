<?php
// models/WorkShift.php - Work Shift Management Model

class WorkShift {
    private $conn;
    private $table_name = "work_shifts";
    private $assignment_table = "employee_shift_assignments";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all work shifts
     * @return array
     */
    public function getAllShifts() {
        try {
            $query = "SELECT ws.*, 
                            COUNT(esa.employee_id) as assigned_employees
                     FROM " . $this->table_name . " ws
                     LEFT JOIN " . $this->assignment_table . " esa ON ws.id = esa.shift_id 
                        AND (esa.end_date IS NULL OR esa.end_date >= CURDATE())
                     GROUP BY ws.id
                     ORDER BY ws.shift_name";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting all shifts: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get active work shifts
     * @return array
     */
    public function getActiveShifts() {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                     WHERE is_active = 1 
                     ORDER BY start_time";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting active shifts: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get shift by ID
     * @param int $shift_id
     * @return array|null
     */
    public function getShiftById($shift_id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = :shift_id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':shift_id', $shift_id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting shift by ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create new work shift
     * @param array $data
     * @return bool
     */
    public function createShift($data) {
        try {
            $query = "INSERT INTO " . $this->table_name . " 
                     (shift_name, start_time, end_time, break_start, break_end, 
                      work_days, is_active, created_at) 
                     VALUES (:shift_name, :start_time, :end_time, :break_start, 
                             :break_end, :work_days, :is_active, NOW())";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':shift_name', $data['shift_name']);
            $stmt->bindParam(':start_time', $data['start_time']);
            $stmt->bindParam(':end_time', $data['end_time']);
            $stmt->bindParam(':break_start', $data['break_start']);
            $stmt->bindParam(':break_end', $data['break_end']);
            $stmt->bindParam(':work_days', $data['work_days']);
            $stmt->bindParam(':is_active', $data['is_active']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error creating shift: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update work shift
     * @param int $shift_id
     * @param array $data
     * @return bool
     */
    public function updateShift($shift_id, $data) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                     SET shift_name = :shift_name, 
                         start_time = :start_time, 
                         end_time = :end_time, 
                         break_start = :break_start, 
                         break_end = :break_end, 
                         work_days = :work_days, 
                         is_active = :is_active,
                         updated_at = NOW()
                     WHERE id = :shift_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':shift_id', $shift_id);
            $stmt->bindParam(':shift_name', $data['shift_name']);
            $stmt->bindParam(':start_time', $data['start_time']);
            $stmt->bindParam(':end_time', $data['end_time']);
            $stmt->bindParam(':break_start', $data['break_start']);
            $stmt->bindParam(':break_end', $data['break_end']);
            $stmt->bindParam(':work_days', $data['work_days']);
            $stmt->bindParam(':is_active', $data['is_active']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating shift: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete work shift
     * @param int $shift_id
     * @return bool
     */
    public function deleteShift($shift_id) {
        try {
            $this->conn->beginTransaction();
            
            // First, remove all assignments for this shift
            $query1 = "DELETE FROM " . $this->assignment_table . " WHERE shift_id = :shift_id";
            $stmt1 = $this->conn->prepare($query1);
            $stmt1->bindParam(':shift_id', $shift_id);
            $stmt1->execute();
            
            // Then delete the shift
            $query2 = "DELETE FROM " . $this->table_name . " WHERE id = :shift_id";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->bindParam(':shift_id', $shift_id);
            $stmt2->execute();
            
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollback();
            error_log("Error deleting shift: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Assign employee to shift
     * @param int $employee_id
     * @param int $shift_id
     * @param string $start_date
     * @param string|null $end_date
     * @return bool
     */
    public function assignEmployeeToShift($employee_id, $shift_id, $start_date, $end_date = null) {
        try {
            // Check if employee is already assigned to this shift for overlapping period
            if ($this->hasConflictingAssignment($employee_id, $shift_id, $start_date, $end_date)) {
                return false;
            }
            
            $query = "INSERT INTO " . $this->assignment_table . " 
                     (employee_id, shift_id, start_date, end_date, created_at) 
                     VALUES (:employee_id, :shift_id, :start_date, :end_date, NOW())";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':employee_id', $employee_id);
            $stmt->bindParam(':shift_id', $shift_id);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error assigning employee to shift: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove employee from shift
     * @param int $employee_id
     * @param int $shift_id
     * @param string|null $end_date
     * @return bool
     */
    public function removeEmployeeFromShift($employee_id, $shift_id, $end_date = null) {
        try {
            if ($end_date) {
                // Set end date for the assignment
                $query = "UPDATE " . $this->assignment_table . " 
                         SET end_date = :end_date, updated_at = NOW()
                         WHERE employee_id = :employee_id AND shift_id = :shift_id 
                         AND (end_date IS NULL OR end_date > :end_date)";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':employee_id', $employee_id);
                $stmt->bindParam(':shift_id', $shift_id);
                $stmt->bindParam(':end_date', $end_date);
            } else {
                // Remove assignment completely
                $query = "DELETE FROM " . $this->assignment_table . " 
                         WHERE employee_id = :employee_id AND shift_id = :shift_id";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':employee_id', $employee_id);
                $stmt->bindParam(':shift_id', $shift_id);
            }
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error removing employee from shift: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get employees assigned to a shift
     * @param int $shift_id
     * @param string|null $date
     * @return array
     */
    public function getEmployeesInShift($shift_id, $date = null) {
        try {
            $current_date = $date ?? date('Y-m-d');
            
            $query = "SELECT e.id, e.emp_code, e.first_name, e.last_name, 
                            d.name_th as department_name, p.name_th as position_name,
                            esa.start_date, esa.end_date
                     FROM " . $this->assignment_table . " esa
                     JOIN employees e ON esa.employee_id = e.id
                     LEFT JOIN departments d ON e.department_id = d.id
                     LEFT JOIN positions p ON e.position_id = p.id
                     WHERE esa.shift_id = :shift_id 
                     AND esa.start_date <= :current_date
                     AND (esa.end_date IS NULL OR esa.end_date >= :current_date)
                     AND e.status = 'ทำงาน'
                     ORDER BY e.first_name, e.last_name";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':shift_id', $shift_id);
            $stmt->bindParam(':current_date', $current_date);
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting employees in shift: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get employee's current shift
     * @param int $employee_id
     * @param string|null $date
     * @return array|null
     */
    public function getEmployeeShift($employee_id, $date = null) {
        try {
            $current_date = $date ?? date('Y-m-d');
            
            $query = "SELECT ws.*, esa.start_date, esa.end_date
                     FROM " . $this->assignment_table . " esa
                     JOIN " . $this->table_name . " ws ON esa.shift_id = ws.id
                     WHERE esa.employee_id = :employee_id 
                     AND esa.start_date <= :current_date
                     AND (esa.end_date IS NULL OR esa.end_date >= :current_date)
                     AND ws.is_active = 1
                     ORDER BY esa.start_date DESC
                     LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':employee_id', $employee_id);
            $stmt->bindParam(':current_date', $current_date);
            
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting employee shift: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get shift schedule for date range
     * @param string $start_date
     * @param string $end_date
     * @param int|null $employee_id
     * @return array
     */
    public function getShiftSchedule($start_date, $end_date, $employee_id = null) {
        try {
            $query = "SELECT ws.*, e.id as employee_id, e.emp_code, e.first_name, e.last_name,
                            esa.start_date as assignment_start, esa.end_date as assignment_end,
                            d.name_th as department_name
                     FROM " . $this->assignment_table . " esa
                     JOIN " . $this->table_name . " ws ON esa.shift_id = ws.id
                     JOIN employees e ON esa.employee_id = e.id
                     LEFT JOIN departments d ON e.department_id = d.id
                     WHERE ws.is_active = 1 
                     AND e.status = 'ทำงาน'
                     AND esa.start_date <= :end_date
                     AND (esa.end_date IS NULL OR esa.end_date >= :start_date)";
            
            if ($employee_id) {
                $query .= " AND e.id = :employee_id";
            }
            
            $query .= " ORDER BY ws.start_time, e.first_name, e.last_name";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
            
            if ($employee_id) {
                $stmt->bindParam(':employee_id', $employee_id);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting shift schedule: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Check for conflicting shift assignments
     * @param int $employee_id
     * @param int $shift_id
     * @param string $start_date
     * @param string|null $end_date
     * @return bool
     */
    private function hasConflictingAssignment($employee_id, $shift_id, $start_date, $end_date = null) {
        try {
            $query = "SELECT COUNT(*) as count FROM " . $this->assignment_table . " 
                     WHERE employee_id = :employee_id 
                     AND shift_id = :shift_id
                     AND start_date <= :check_end_date
                     AND (end_date IS NULL OR end_date >= :start_date)";
            
            $check_end_date = $end_date ?? '9999-12-31';
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':employee_id', $employee_id);
            $stmt->bindParam(':shift_id', $shift_id);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':check_end_date', $check_end_date);
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Error checking conflicting assignment: " . $e->getMessage());
            return true; // Return true to be safe
        }
    }

    /**
     * Get shift statistics
     * @return array
     */
    public function getShiftStatistics() {
        try {
            $stats = [
                'total_shifts' => 0,
                'active_shifts' => 0,
                'total_assignments' => 0,
                'employees_with_shifts' => 0
            ];
            
            // Count total shifts
            $query1 = "SELECT COUNT(*) as total FROM " . $this->table_name;
            $stmt1 = $this->conn->prepare($query1);
            $stmt1->execute();
            $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);
            $stats['total_shifts'] = $result1['total'];
            
            // Count active shifts
            $query2 = "SELECT COUNT(*) as active FROM " . $this->table_name . " WHERE is_active = 1";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->execute();
            $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
            $stats['active_shifts'] = $result2['active'];
            
            // Count current assignments
            $query3 = "SELECT COUNT(*) as assignments FROM " . $this->assignment_table . " 
                      WHERE end_date IS NULL OR end_date >= CURDATE()";
            $stmt3 = $this->conn->prepare($query3);
            $stmt3->execute();
            $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);
            $stats['total_assignments'] = $result3['assignments'];
            
            // Count employees with shifts
            $query4 = "SELECT COUNT(DISTINCT employee_id) as employees FROM " . $this->assignment_table . " 
                      WHERE end_date IS NULL OR end_date >= CURDATE()";
            $stmt4 = $this->conn->prepare($query4);
            $stmt4->execute();
            $result4 = $stmt4->fetch(PDO::FETCH_ASSOC);
            $stats['employees_with_shifts'] = $result4['employees'];
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Error getting shift statistics: " . $e->getMessage());
            return [
                'total_shifts' => 0,
                'active_shifts' => 0,
                'total_assignments' => 0,
                'employees_with_shifts' => 0
            ];
        }
    }

    /**
     * Check if employee should be working on a specific date/time based on shift
     * @param int $employee_id
     * @param string $date
     * @param string $time
     * @return bool
     */
    public function isEmployeeScheduledToWork($employee_id, $date, $time) {
        try {
            $shift = $this->getEmployeeShift($employee_id, $date);
            
            if (!$shift) {
                return false;
            }
            
            // Check if today is a work day for this shift
            $day_of_week = date('N', strtotime($date)); // 1 = Monday, 7 = Sunday
            $work_days = explode(',', $shift['work_days']);
            
            if (!in_array($day_of_week, $work_days)) {
                return false;
            }
            
            // Check if time is within shift hours
            $current_time = strtotime($time);
            $shift_start = strtotime($shift['start_time']);
            $shift_end = strtotime($shift['end_time']);
            
            // Handle shifts that cross midnight
            if ($shift_end < $shift_start) {
                return ($current_time >= $shift_start || $current_time <= $shift_end);
            } else {
                return ($current_time >= $shift_start && $current_time <= $shift_end);
            }
        } catch (Exception $e) {
            error_log("Error checking employee schedule: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get work days array with Thai names
     * @return array
     */
    public static function getWorkDaysOptions() {
        return [
            '1' => 'จันทร์',
            '2' => 'อังคาร', 
            '3' => 'พุธ',
            '4' => 'พฤหัสบดี',
            '5' => 'ศุกร์',
            '6' => 'เสาร์',
            '7' => 'อาทิตย์'
        ];
    }

    /**
     * Format work days for display
     * @param string $work_days_string
     * @return string
     */
    public static function formatWorkDays($work_days_string) {
        if (empty($work_days_string)) {
            return '-';
        }
        
        $days = explode(',', $work_days_string);
        $day_names = self::getWorkDaysOptions();
        $formatted_days = [];
        
        foreach ($days as $day) {
            if (isset($day_names[$day])) {
                $formatted_days[] = $day_names[$day];
            }
        }
        
        return implode(', ', $formatted_days);
    }

    /**
     * Calculate shift duration in hours
     * @param string $start_time
     * @param string $end_time
     * @param string|null $break_start
     * @param string|null $break_end
     * @return float
     */
    public static function calculateShiftDuration($start_time, $end_time, $break_start = null, $break_end = null) {
        try {
            $start = new DateTime($start_time);
            $end = new DateTime($end_time);
            
            // Handle shifts that cross midnight
            if ($end < $start) {
                $end->add(new DateInterval('P1D'));
            }
            
            $duration = $start->diff($end);
            $hours = $duration->h + ($duration->i / 60);
            
            // Subtract break time if provided
            if ($break_start && $break_end) {
                $break_start_time = new DateTime($break_start);
                $break_end_time = new DateTime($break_end);
                
                if ($break_end_time < $break_start_time) {
                    $break_end_time->add(new DateInterval('P1D'));
                }
                
                $break_duration = $break_start_time->diff($break_end_time);
                $break_hours = $break_duration->h + ($break_duration->i / 60);
                
                $hours -= $break_hours;
            }
            
            return round($hours, 2);
        } catch (Exception $e) {
            error_log("Error calculating shift duration: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get shift color for display
     * @param int $shift_id
     * @return string
     */
    public static function getShiftColor($shift_id) {
        $colors = [
            '#3B82F6', // Blue
            '#10B981', // Green  
            '#F59E0B', // Yellow
            '#EF4444', // Red
            '#8B5CF6', // Purple
            '#06B6D4', // Cyan
            '#F97316', // Orange
            '#84CC16'  // Lime
        ];
        
        return $colors[($shift_id - 1) % count($colors)];
    }
}
?>