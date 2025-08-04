<?php
// models/Payroll.php

class Payroll
{
    private $conn;
    private $table_name = "payrolls";

    // Properties
    public $id;
    public $employee_id;
    public $pay_period_start;
    public $pay_period_end;
    public $base_salary;
    public $overtime_pay;
    public $allowances;
    public $late_deductions;
    public $absence_deductions;
    public $social_security;
    public $tax;
    public $net_salary;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * สร้างและบันทึกข้อมูลเงินเดือน
     */
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " SET
                    employee_id = :employee_id,
                    pay_period_start = :pay_period_start,
                    pay_period_end = :pay_period_end,
                    base_salary = :base_salary,
                    overtime_pay = :overtime_pay,
                    allowances = :allowances,
                    late_deductions = :late_deductions,
                    absence_deductions = :absence_deductions,
                    social_security = :social_security,
                    tax = :tax,
                    net_salary = :net_salary";

        $stmt = $this->conn->prepare($query);

        // Bind data
        $stmt->bindParam(":employee_id", $this->employee_id);
        $stmt->bindParam(":pay_period_start", $this->pay_period_start);
        $stmt->bindParam(":pay_period_end", $this->pay_period_end);
        $stmt->bindParam(":base_salary", $this->base_salary);
        $stmt->bindParam(":overtime_pay", $this->overtime_pay);
        $stmt->bindParam(":allowances", $this->allowances);
        $stmt->bindParam(":late_deductions", $this->late_deductions);
        $stmt->bindParam(":absence_deductions", $this->absence_deductions);
        $stmt->bindParam(":social_security", $this->social_security);
        $stmt->bindParam(":tax", $this->tax);
        $stmt->bindParam(":net_salary", $this->net_salary);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * ดึงรายการเงินเดือนที่สร้างแล้วทั้งหมดตามงวด
     */
    public function readByPeriod($month, $year)
    {
        $start_date = "$year-$month-01";
        $end_date = date("Y-m-t", strtotime($start_date));

        $query = "SELECT
                    p.*,
                    CONCAT(e.first_name_th, ' ', e.last_name_th) as employee_name
                  FROM
                    " . $this->table_name . " p
                    JOIN employees e ON p.employee_id = e.id
                  WHERE
                    p.pay_period_start = ?
                  ORDER BY
                    e.first_name_th ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $start_date);
        $stmt->execute();
        return $stmt;
    }

    /**
     * ดึงข้อมูลสลิปเงินเดือน 1 รายการ (แก้ไขแล้ว)
     */
    public function readOne($id)
    {
        $query = "SELECT
                    p.*,
                    e.employee_code as emp_code, -- <<< แก้จาก e.employee_id เป็น e.employee_code
                    CONCAT(e.prefix, e.first_name_th, ' ', e.last_name_th) as employee_name,
                    pos.name_th as position_name,
                    dep.name_th as department_name
                  FROM
                    " . $this->table_name . " p
                    JOIN employees e ON p.employee_id = e.id
                    LEFT JOIN positions pos ON e.position_id = pos.id
                    LEFT JOIN departments dep ON e.department_id = dep.id
                  WHERE
                    p.id = ?
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * ตรวจสอบว่าเคยสร้างเงินเดือนของงวดนั้นๆ แล้วหรือยัง
     */
    public function checkIfGenerated($month, $year)
    {
        $start_date = "$year-$month-01";
        $query = "SELECT COUNT(id) as count FROM " . $this->table_name . " WHERE pay_period_start = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $start_date);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }

    /**
     * ฟังก์ชันหลักสำหรับคำนวณเงินเดือน (ตัวอย่างแบบง่าย)
     */
    public function calculatePayrollForEmployee($employee_id, $month, $year)
    {
        // NOTE: This is a simplified calculation logic.
        // Real-world scenarios require more complex rules for OT, tax, and deductions.

        // 1. Get Employee's base salary
        $emp_query = "SELECT salary FROM employees WHERE id = ? LIMIT 1";
        $stmt_emp = $this->conn->prepare($emp_query);
        $stmt_emp->bindParam(1, $employee_id);
        $stmt_emp->execute();
        $emp_row = $stmt_emp->fetch(PDO::FETCH_ASSOC);
        $base_salary = $emp_row['salary'] ?? 0;

        // 2. Calculate OT, Deductions (this part needs data from attendance)
        // For this example, we'll use placeholder values.
        $overtime_pay = 0; // TODO: Calculate from attendance records
        $late_deductions = 0; // TODO: Calculate from attendance records
        $absence_deductions = 0; // TODO: Calculate from attendance records

        // 3. Social Security
        $soc_sec_base = min($base_salary, 15000);
        $social_security = $soc_sec_base * 0.05; // 5%

        // 4. Tax (Simplified)
        $taxable_income = $base_salary * 12;
        $tax = 0;
        if ($taxable_income > 150000) {
            $tax = (($taxable_income - 150000) * 0.05) / 12; // 5% on income above 150k
        }

        // 5. Net Salary
        $net_salary = ($base_salary + $overtime_pay) - ($late_deductions + $absence_deductions + $social_security + $tax);

        return [
            'base_salary' => $base_salary,
            'overtime_pay' => $overtime_pay,
            'allowances' => 0, // Placeholder
            'late_deductions' => $late_deductions,
            'absence_deductions' => $absence_deductions,
            'social_security' => $social_security,
            'tax' => $tax,
            'net_salary' => $net_salary
        ];
    }
    /**
     * ดึงประวัติสลิปเงินเดือนทั้งหมดของพนักงาน 1 คน
     * @param int $employee_id
     * @return PDOStatement
     */
    public function readForEmployee($employee_id)
    {
        $query = "SELECT
                    id,
                    net_salary,
                    generated_at,
                    pay_period_start,
                    pay_period_end
                  FROM
                    " . $this->table_name . "
                  WHERE
                    employee_id = ?
                  ORDER BY
                    pay_period_start DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $employee_id);
        $stmt->execute();
        return $stmt;
    }
}
