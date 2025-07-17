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

    // Constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // ค้นหาผู้ใช้ด้วยอีเมล
    function findByEmail() {
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
}
?>
