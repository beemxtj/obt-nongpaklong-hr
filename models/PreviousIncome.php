<?php
// models/PreviousIncome.php

class PreviousIncome {
    private $conn;
    private $table_name = "previous_incomes";

    public function __construct($db) {
        $this->conn = $db;
    }

    // ดึงข้อมูลรายได้สะสมของพนักงานตามปี
    public function findByEmployeeAndYear($employee_id, $year) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE employee_id = ? AND tax_year = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$employee_id, $year]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // บันทึกหรืออัปเดตข้อมูลรายได้สะสม
    public function save($employee_id, $year, $income, $tax, $social, $provident) {
        $query = "INSERT INTO " . $this->table_name . " (employee_id, tax_year, total_income, total_tax, social_security, provident_fund)
                  VALUES (?, ?, ?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE 
                    total_income = VALUES(total_income),
                    total_tax = VALUES(total_tax),
                    social_security = VALUES(social_security),
                    provident_fund = VALUES(provident_fund)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$employee_id, $year, $income, $tax, $social, $provident]);
    }
}
?>
