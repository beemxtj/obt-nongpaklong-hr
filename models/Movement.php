<?php
// models/Movement.php

class Movement {
    private $conn;
    private $table_name = "employee_movements";

    public $id;
    public $employee_id;
    public $effective_date;
    public $movement_type;
    public $details;
    public $created_by;

    public function __construct($db) {
        $this->conn = $db;
    }

    // สร้างรายการความเคลื่อนไหวใหม่
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET
                    employee_id = :employee_id,
                    effective_date = :effective_date,
                    movement_type = :movement_type,
                    details = :details,
                    created_by = :created_by";
        
        $stmt = $this->conn->prepare($query);

        // Bind data
        $stmt->bindParam(':employee_id', $this->employee_id);
        $stmt->bindParam(':effective_date', $this->effective_date);
        $stmt->bindParam(':movement_type', $this->movement_type);
        $stmt->bindParam(':details', $this->details);
        $stmt->bindParam(':created_by', $this->created_by);

        return $stmt->execute();
    }
}
?>
