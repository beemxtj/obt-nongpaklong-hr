<?php
// models/Payslip.php
require_once __DIR__ . '/PreviousIncome.php';

class Payslip {
    private $conn;
    private $table_name = "payslips";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function generatePayslipsForMonth($month, $year) {
        $previousIncomeModel = new PreviousIncome($this->conn);

        $query_emp = "SELECT id, salary, start_date, provident_fund_rate_employee FROM employees WHERE status IN ('ทำงาน', 'ทดลองงาน')";
        $stmt_emp = $this->conn->prepare($query_emp);
        $stmt_emp->execute();
        
        $count = 0;
        while ($emp = $stmt_emp->fetch(PDO::FETCH_ASSOC)) {
            // --- BUG FIX: Ensure base_salary is never null, default to 0.00 if it is. ---
            $base_salary = $emp['salary'] ?? 0.00;

            // Calculate estimated annual income
            $annual_income = $base_salary * 12;
            $start_year = date('Y', strtotime($emp['start_date']));
            if ($start_year == $year) {
                $prev_income_data = $previousIncomeModel->findByEmployeeAndYear($emp['id'], $year);
                if ($prev_income_data) {
                    $annual_income += $prev_income_data['total_income'];
                }
            }

            // Simplified progressive tax calculation
            $taxable_income = $annual_income - 60000;
            $annual_tax = 0;
            if ($taxable_income > 500000) { $annual_tax += ($taxable_income - 500000) * 0.20; $taxable_income = 500000; }
            if ($taxable_income > 300000) { $annual_tax += ($taxable_income - 300000) * 0.15; $taxable_income = 300000; }
            if ($taxable_income > 150000) { $annual_tax += ($taxable_income - 150000) * 0.10; $taxable_income = 150000; }
            if ($taxable_income > 0) { $annual_tax += $taxable_income * 0.05; }
            $monthly_tax = $annual_tax > 0 ? $annual_tax / 12 : 0;

            // Deductions
            $social_security = min($base_salary * 0.05, 750);
            $provident_fund = $base_salary * ($emp['provident_fund_rate_employee'] / 100);
            $ot_pay = 0; // Placeholder for OT

            // Totals
            $total_earnings = $base_salary + $ot_pay;
            $total_deductions = $social_security + $provident_fund + $monthly_tax;
            $net_salary = $total_earnings - $total_deductions;

            // Database Save
            $query_save = "INSERT INTO " . $this->table_name . " (employee_id, pay_period_month, pay_period_year, base_salary, ot_pay, total_earnings, tax_deduction, social_security_deduction, provident_fund_deduction, total_deductions, net_salary)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                           ON DUPLICATE KEY UPDATE base_salary=VALUES(base_salary), ot_pay=VALUES(ot_pay), total_earnings=VALUES(total_earnings), tax_deduction=VALUES(tax_deduction), social_security_deduction=VALUES(social_security_deduction), provident_fund_deduction=VALUES(provident_fund_deduction), total_deductions=VALUES(total_deductions), net_salary=VALUES(net_salary)";
            $stmt_save = $this->conn->prepare($query_save);
            // This is Line 54, where the error occurred
            $stmt_save->execute([$emp['id'], $month, $year, $base_salary, $ot_pay, $total_earnings, $monthly_tax, $social_security, $provident_fund, $total_deductions, $net_salary]);
            $count++;
        }
        return ['count' => $count];
    }

    public function readByEmployee($employee_id) {
        $query = "SELECT id, pay_period_month, pay_period_year, net_salary, generated_at
                  FROM " . $this->table_name . "
                  WHERE employee_id = ?
                  ORDER BY pay_period_year DESC, pay_period_month DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $employee_id);
        $stmt->execute();
        return $stmt;
    }

    public function readOne($payslip_id, $employee_id = null) {
        $query = "SELECT p.*, 
                         e.employee_code, e.prefix, e.first_name_th, e.last_name_th, e.bank_name, e.bank_account_number,
                         pos.name_th as position_name,
                         dep.name_th as department_name
                  FROM " . $this->table_name . " p
                  JOIN employees e ON p.employee_id = e.id
                  LEFT JOIN positions pos ON e.position_id = pos.id
                  LEFT JOIN departments dep ON e.department_id = dep.id
                  WHERE p.id = ?";

        if ($employee_id !== null) {
            $query .= " AND p.employee_id = ?";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $payslip_id);
        if ($employee_id !== null) {
            $stmt->bindParam(2, $employee_id);
        }
        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>