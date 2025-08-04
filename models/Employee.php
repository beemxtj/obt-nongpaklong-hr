<?php
// models/Employee.php
class Employee {
    private $conn;
    private $table_name = "employees";

    // --- All Employee Properties ---
    public $id, $employee_code, $password, $prefix, $first_name_th, $last_name_th, $first_name_en, $last_name_en, $full_name;
    public $gender, $birth_date, $nationality, $email, $phone_number, $work_phone, $national_id;
    public $address_line1, $district, $province, $postal_code, $profile_image_path;
    public $start_date, $probation_days, $status, $position_id, $department_id, $supervisor_id, $role_id;
    public $salary, $bank_name, $bank_account_number, $tax_id, $provident_fund_rate_employee, $provident_fund_rate_company;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Reads all employees with filtering and searching.
     */
    public function read() {
        $query = "SELECT
                    e.id, e.employee_code, e.prefix, e.first_name_th, e.last_name_th,
                    CONCAT(e.prefix, e.first_name_th, ' ', e.last_name_th) as full_name,
                    e.status, e.profile_image_path, p.name_th as position_name, d.name_th as department_name
                FROM " . $this->table_name . " e
                LEFT JOIN positions p ON e.position_id = p.id
                LEFT JOIN departments d ON e.department_id = d.id
                WHERE 1=1";

        $params = [];
        if (!empty($_GET['search'])) {
            $search_term = "%" . $_GET['search'] . "%";
            $query .= " AND (CONCAT(e.first_name_th, ' ', e.last_name_th) LIKE :search OR e.employee_code LIKE :search)";
            $params[':search'] = $search_term;
        }

        if (!empty($_GET['status'])) {
            $query .= " AND e.status = :status";
            $params[':status'] = $_GET['status'];
        }

        $query .= " ORDER BY e.employee_code ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Reads a single employee's complete record by ID.
     */
    public function readOne() {
        $query = "SELECT e.*, CONCAT(e.prefix, e.first_name_th, ' ', e.last_name_th) as full_name 
                  FROM " . $this->table_name . " e
                  WHERE e.id = ? 
                  LIMIT 1";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            foreach ($row as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
    }

    /**
     * Check if email already exists for another employee
     */
    public function emailExists($email, $exclude_id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }
        $query .= " LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    /**
     * Check if employee code already exists for another employee
     */
    public function employeeCodeExists($employee_code, $exclude_id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE employee_code = :employee_code";
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }
        $query .= " LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_code', $employee_code);
        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    /**
     * Creates a new employee record in the database.
     */
    public function create() {
        // Check for duplicate email
        if ($this->emailExists($this->email)) {
            throw new Exception("อีเมลนี้มีการใช้งานแล้วในระบบ");
        }

        // Check for duplicate employee code
        if ($this->employeeCodeExists($this->employee_code)) {
            throw new Exception("รหัสพนักงานนี้มีการใช้งานแล้วในระบบ");
        }

        // Generate employee code if not provided
        if (empty($this->employee_code)) {
            $this->employee_code = $this->generateNewEmployeeCode();
        }

        $query = "INSERT INTO " . $this->table_name . " SET
                    employee_code=:employee_code, password=:password, prefix=:prefix, first_name_th=:first_name_th, last_name_th=:last_name_th,
                    first_name_en=:first_name_en, last_name_en=:last_name_en, gender=:gender, birth_date=:birth_date, nationality=:nationality,
                    email=:email, phone_number=:phone_number, work_phone=:work_phone, national_id=:national_id, 
                    address_line1=:address_line1, district=:district, province=:province, postal_code=:postal_code,
                    start_date=:start_date, probation_days=:probation_days, status=:status, 
                    position_id=:position_id, department_id=:department_id, supervisor_id=:supervisor_id, role_id=:role_id,
                    salary=:salary, bank_name=:bank_name, bank_account_number=:bank_account_number, tax_id=:tax_id,
                    provident_fund_rate_employee=:provident_fund_rate_employee, provident_fund_rate_company=:provident_fund_rate_company,
                    profile_image_path=:profile_image_path";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":employee_code", $this->employee_code);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":prefix", $this->prefix);
        $stmt->bindParam(":first_name_th", $this->first_name_th);
        $stmt->bindParam(":last_name_th", $this->last_name_th);
        $stmt->bindParam(":first_name_en", $this->first_name_en);
        $stmt->bindParam(":last_name_en", $this->last_name_en);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":birth_date", $this->birth_date);
        $stmt->bindParam(":nationality", $this->nationality);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone_number", $this->phone_number);
        $stmt->bindParam(":work_phone", $this->work_phone);
        $stmt->bindParam(":national_id", $this->national_id);
        $stmt->bindParam(":address_line1", $this->address_line1);
        $stmt->bindParam(":district", $this->district);
        $stmt->bindParam(":province", $this->province);
        $stmt->bindParam(":postal_code", $this->postal_code);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":probation_days", $this->probation_days);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":position_id", $this->position_id);
        $stmt->bindParam(":department_id", $this->department_id);
        $stmt->bindParam(":supervisor_id", $this->supervisor_id);
        $stmt->bindParam(":role_id", $this->role_id);
        $stmt->bindParam(":salary", $this->salary);
        $stmt->bindParam(":bank_name", $this->bank_name);
        $stmt->bindParam(":bank_account_number", $this->bank_account_number);
        $stmt->bindParam(":tax_id", $this->tax_id);
        $stmt->bindParam(":provident_fund_rate_employee", $this->provident_fund_rate_employee);
        $stmt->bindParam(":provident_fund_rate_company", $this->provident_fund_rate_company);
        $stmt->bindParam(":profile_image_path", $this->profile_image_path);

        return $stmt->execute();
    }

    /**
     * Updates a complete employee record.
     */
    public function update() {
        // Check for duplicate email (excluding current employee)
        if ($this->emailExists($this->email, $this->id)) {
            throw new Exception("อีเมลนี้มีการใช้งานแล้วในระบบ");
        }

        // Check for duplicate employee code (excluding current employee)
        if ($this->employeeCodeExists($this->employee_code, $this->id)) {
            throw new Exception("รหัสพนักงานนี้มีการใช้งานแล้วในระบบ");
        }

        $password_query_part = !empty($this->password) ? "password = :password," : "";
        
        $query = "UPDATE " . $this->table_name . " SET 
                    employee_code = :employee_code,
                    {$password_query_part}
                    prefix = :prefix,
                    first_name_th = :first_name_th,
                    last_name_th = :last_name_th,
                    first_name_en = :first_name_en,
                    last_name_en = :last_name_en,
                    gender = :gender,
                    birth_date = :birth_date,
                    nationality = :nationality,
                    email = :email,
                    phone_number = :phone_number,
                    work_phone = :work_phone,           
                    national_id = :national_id,
                    address_line1 = :address_line1,     
                    district = :district,               
                    province = :province,               
                    postal_code = :postal_code,         
                    start_date = :start_date,
                    probation_days = :probation_days,
                    status = :status,
                    position_id = :position_id,
                    department_id = :department_id,
                    supervisor_id = :supervisor_id,
                    role_id = :role_id,
                    salary = :salary,
                    bank_name = :bank_name,
                    bank_account_number = :bank_account_number,
                    tax_id = :tax_id,
                    provident_fund_rate_employee = :provident_fund_rate_employee,
                    provident_fund_rate_company = :provident_fund_rate_company,
                    profile_image_path = :profile_image_path
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":employee_code", $this->employee_code);
        $stmt->bindParam(":prefix", $this->prefix);
        $stmt->bindParam(":first_name_th", $this->first_name_th);
        $stmt->bindParam(":last_name_th", $this->last_name_th);
        $stmt->bindParam(":first_name_en", $this->first_name_en);
        $stmt->bindParam(":last_name_en", $this->last_name_en);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":birth_date", $this->birth_date);
        $stmt->bindParam(":nationality", $this->nationality);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone_number", $this->phone_number);
        $stmt->bindParam(":work_phone", $this->work_phone);
        $stmt->bindParam(":national_id", $this->national_id);
        $stmt->bindParam(":address_line1", $this->address_line1);
        $stmt->bindParam(":district", $this->district);
        $stmt->bindParam(":province", $this->province);
        $stmt->bindParam(":postal_code", $this->postal_code);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":probation_days", $this->probation_days);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":position_id", $this->position_id);
        $stmt->bindParam(":department_id", $this->department_id);
        $stmt->bindParam(":supervisor_id", $this->supervisor_id);
        $stmt->bindParam(":role_id", $this->role_id);
        $stmt->bindParam(":salary", $this->salary);
        $stmt->bindParam(":bank_name", $this->bank_name);
        $stmt->bindParam(":bank_account_number", $this->bank_account_number);
        $stmt->bindParam(":tax_id", $this->tax_id);
        $stmt->bindParam(":provident_fund_rate_employee", $this->provident_fund_rate_employee);
        $stmt->bindParam(":provident_fund_rate_company", $this->provident_fund_rate_company);
        $stmt->bindParam(":profile_image_path", $this->profile_image_path);

        if (!empty($this->password)) {
            $stmt->bindParam(':password', $this->password);
        }
        
        return $stmt->execute(); 
    }

    /**
     * Deletes an employee record.
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }

    /**
     * Generates a new unique employee code.
     */
    public function generateNewEmployeeCode() {
        $query = "SELECT id FROM " . $this->table_name . " ORDER BY id DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $next_id = $row ? $row['id'] + 1 : 1;
        return "EMP" . str_pad($next_id, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Gets a list of employees for use in supervisor dropdowns.
     * @param int|null $exclude_id The ID of the employee to exclude from the list.
     */
    public function getSupervisorList($exclude_id = null) {
        $query = "SELECT id, CONCAT(prefix, first_name_th, ' ', last_name_th) as full_name 
                  FROM " . $this->table_name . "
                  WHERE status != 'ลาออก'";
        if ($exclude_id !== null) {
            $query .= " AND id != ?";
        }
        $query .= " ORDER BY first_name_th";
        
        $stmt = $this->conn->prepare($query);
        if ($exclude_id !== null) {
            $stmt->bindParam(1, $exclude_id);
        }
        $stmt->execute();
        return $stmt;
    }

    /**
     * Reads a single employee by employee_code.
     */
    public function readOneByEmployeeCode() {
        $query = "SELECT e.*, CONCAT(e.prefix, e.first_name_th, ' ', e.last_name_th) as full_name 
                  FROM " . $this->table_name . " e
                  WHERE e.employee_code = ? 
                  LIMIT 1";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->employee_code);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            foreach ($row as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
        return $this;
    }

    // --- Helper functions for dropdowns ---
    public function readPositions() { 
        $query = "SELECT id, name_th FROM positions ORDER BY name_th"; 
        $stmt = $this->conn->prepare($query); 
        $stmt->execute(); 
        return $stmt; 
    }
    public function readDepartments() { 
        $query = "SELECT id, name_th FROM departments ORDER BY name_th"; 
        $stmt = $this->conn->prepare($query); 
        $stmt->execute(); 
        return $stmt; 
    }
    public function readRoles() { 
        $query = "SELECT id, role_name FROM roles ORDER BY id"; 
        $stmt = $this->conn->prepare($query); 
        $stmt->execute(); 
        return $stmt; 
    }
}
?>