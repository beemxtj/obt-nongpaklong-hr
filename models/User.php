<?php
// models/User.php

class User {
    // Database connection and table name
    private $conn;
    private $table_name = "employees";

    // Employee Properties
    public $id;
    public $employee_code;
    public $email;
    public $password;
    public $first_name_th;
    public $role_id;

<<<<<<< HEAD
    /**
     * Line user ID associated with the employee.
     *
     * This property is intentionally declared to make it explicit that the
     * `employees` table contains a `line_user_id` column used to link a
     * LINE account to an employee record.  While the property is not used
     * directly in every method of this class, its presence allows the
     * application to assign and update the value safely.  See
     * {@see updateLineUserId()} for details on how it is persisted.
     *
     * @var string|null
     */
    public $line_user_id;

=======
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
    // Constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // ค้นหาผู้ใช้ด้วยอีเมล
    function findByEmail() {
<<<<<<< HEAD
        // ===== จุดที่แก้ไข: JOIN ตาราง roles เพื่อดึง permissions =====
        $query = "SELECT
                    e.id, e.employee_code, e.password, e.first_name_th, e.role_id, r.permissions
                FROM
                    " . $this->table_name . " e
                LEFT JOIN
                    roles r ON e.role_id = r.id
                WHERE
                    e.email = :email
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Find a user by their LINE user ID.
     *
     * This method is used during the LINE OAuth callback to determine whether
     * a LINE account has already been linked to an employee record.  It
     * returns a PDO statement so that the caller can check rowCount() and
     * fetch the associated employee data (including role permissions) if
     * present.
     *
     * @param string $line_user_id The LINE user ID to search for.
     * @return PDOStatement
     */
    public function findByLineUserId($line_user_id) {
        $query = "SELECT
                    e.id, e.employee_code, e.password, e.first_name_th, e.role_id, r.permissions
                  FROM
                    " . $this->table_name . " e
                  LEFT JOIN
                    roles r ON e.role_id = r.id
                  WHERE
                    e.line_user_id = :line_user_id
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':line_user_id', $line_user_id);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Find a user by their employee code.
     *
     * When linking a LINE account to an existing employee, the user is asked
     * to supply their employee code.  This method searches the employees table
     * by that code and joins the roles table so that permissions are
     * available for session setup.  It uses the instance property
     * `$employee_code`, so callers should set that property before invoking
     * the method.
     *
     * @return PDOStatement
     */
    public function findByEmployeeCode() {
        $query = "SELECT
                    e.id, e.employee_code, e.password, e.first_name_th, e.role_id, r.permissions
                  FROM
                    " . $this->table_name . " e
                  LEFT JOIN
                    roles r ON e.role_id = r.id
                  WHERE
                    e.employee_code = :employee_code
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_code', $this->employee_code);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Update the LINE user ID for a specific employee.
     *
     * This helper method writes the provided LINE user ID to the
     * `line_user_id` column of the employees table.  It is used when a user
     * successfully links their LINE account to their internal employee record.
     *
     * @param int    $employeeId   The primary key of the employee to update.
     * @param string $line_user_id The LINE user ID to associate.
     * @return bool True on success, false otherwise.
     */
    public function updateLineUserId($employeeId, $line_user_id) {
        $query = "UPDATE " . $this->table_name . "
                  SET line_user_id = :line_user_id
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':line_user_id', $line_user_id);
        $stmt->bindParam(':id', $employeeId);
        return $stmt->execute();
    }
=======
        // Query to read single record
        $query = "SELECT
                    id, employee_code, password, first_name_th, role_id
                FROM
                    " . $this->table_name . "
                WHERE
                    email = :email
                LIMIT
                    0,1";

        // Prepare query statement
        $stmt = $this->conn->prepare($query);

        // Bind email
        $stmt->bindParam(':email', $this->email);

        // Execute query
        $stmt->execute();

        return $stmt;
    }
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
}
?>
