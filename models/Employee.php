<?php
// models/Employee.php
class Employee {
    private $conn;
    private $table_name = "employees";

    // Properties
    public $id, $employee_code, $password, $prefix, $first_name_th, $last_name_th, $email, $start_date, $status, $position_id, $department_id, $supervisor_id, $role_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function generateNewEmployeeCode() {
        $query = "SELECT id FROM " . $this->table_name . " ORDER BY id DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $next_id = ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_ASSOC)['id'] + 1 : 1;
        return "EMP" . str_pad($next_id, 3, '0', STR_PAD_LEFT);
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            foreach ($row as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
    }

    public function update() {
        $password_query_part = !empty($this->password) ? "password = :password," : "";
        $query = "UPDATE " . $this->table_name . " SET employee_code = :employee_code, {$password_query_part} prefix = :prefix, first_name_th = :first_name_th, last_name_th = :last_name_th, email = :email, start_date = :start_date, status = :status, position_id = :position_id, department_id = :department_id, supervisor_id = :supervisor_id, role_id = :role_id WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        // Sanitize and bind data
        $this->id=htmlspecialchars(strip_tags($this->id));
        $this->employee_code=htmlspecialchars(strip_tags($this->employee_code));
        // ... (sanitize other properties) ...

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(":employee_code", $this->employee_code);
        $stmt->bindParam(":prefix", $this->prefix);
        $stmt->bindParam(":first_name_th", $this->first_name_th);
        $stmt->bindParam(":last_name_th", $this->last_name_th);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":position_id", $this->position_id);
        $stmt->bindParam(":department_id", $this->department_id);
        $stmt->bindParam(":supervisor_id", $this->supervisor_id);
        $stmt->bindParam(":role_id", $this->role_id);
        
        if (!empty($this->password)) {
            $stmt->bindParam(':password', $this->password);
        }

        return $stmt->execute();
    }

    public function create() {
        $this->employee_code = $this->generateNewEmployeeCode();
        $query = "INSERT INTO " . $this->table_name . " SET employee_code=:employee_code, password=:password, prefix=:prefix, first_name_th=:first_name_th, last_name_th=:last_name_th, email=:email, start_date=:start_date, status=:status, position_id=:position_id, department_id=:department_id, supervisor_id=:supervisor_id, role_id=:role_id";
        $stmt = $this->conn->prepare($query);
        // ... (bind all params like in update) ...
        $stmt->bindParam(":employee_code", $this->employee_code);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":prefix", $this->prefix);
        $stmt->bindParam(":first_name_th", $this->first_name_th);
        $stmt->bindParam(":last_name_th", $this->last_name_th);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":position_id", $this->position_id);
        $stmt->bindParam(":department_id", $this->department_id);
        $stmt->bindParam(":supervisor_id", $this->supervisor_id);
        $stmt->bindParam(":role_id", $this->role_id);
        return $stmt->execute();
    }
    
    // ===== ฟังก์ชันอ่านข้อมูลพนักงานทั้งหมด =====
    function read() {
        // ===== จุดที่แก้ไข: เพิ่ม CONCAT(...) as full_name =====
        $query = "SELECT
                    e.id,
                    e.employee_code,
                    e.prefix,
                    e.first_name_th,
                    e.last_name_th,
                    CONCAT(e.prefix, e.first_name_th, ' ', e.last_name_th) as full_name,
                    e.status,
                    p.name_th as position_name,
                    d.name_th as department_name
                FROM
                    " . $this->table_name . " e
                    LEFT JOIN
                        positions p ON e.position_id = p.id
                    LEFT JOIN
                        departments d ON e.department_id = d.id
                ORDER BY
                    e.employee_code ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }
    
    public function readPositions() { $query = "SELECT id, name_th FROM positions ORDER BY name_th"; $stmt = $this->conn->prepare($query); $stmt->execute(); return $stmt; }
    public function readDepartments() { $query = "SELECT id, name_th FROM departments ORDER BY name_th"; $stmt = $this->conn->prepare($query); $stmt->execute(); return $stmt; }
    public function readRoles() { $query = "SELECT id, role_name FROM roles ORDER BY id"; $stmt = $this->conn->prepare($query); $stmt->execute(); return $stmt; }
}
?>
