<?php
// api/zkteco_listener.php
// API Endpoint for receiving real-time attendance data from ZKTeco devices.

// Include necessary files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/../models/Employee.php';

// --- Main Logic ---
header('Content-Type: text/plain');

// ZKTeco devices often send data as raw POST body
$raw_data = file_get_contents('php://input');

// A common format is one line per record, e.g., "1,EMP001,1,2025-07-18 08:30:00,1,0"
// We will parse this to get the employee code and timestamp.
// Note: This format might need adjustment based on your device's specific configuration.

$lines = explode("\n", trim($raw_data));
$response_message = "";

foreach ($lines as $line) {
    if (empty($line)) continue;

    $parts = explode(",", trim($line));
    
    // Assuming employee code is the 2nd part and timestamp is the 4th part
    if (count($parts) >= 4) {
        $employee_code = trim($parts[1]);
        $timestamp = trim($parts[3]);

        // Connect to Database
        $database = new Database();
        $db = $database->getConnection();

        $attendance = new Attendance($db);
        $result = $attendance->logFromDevice($employee_code, $timestamp);

        if ($result['status']) {
            $response_message .= "OK: Processed record for employee {$employee_code} at {$timestamp}. Action: {$result['action']}\n";
        } else {
            $response_message .= "ERROR: Failed to process record for employee {$employee_code}. Reason: {$result['message']}\n";
        }
    }
}

// ZKTeco devices expect a response like "OK" to confirm data reception.
echo "OK";

// For debugging purposes, you can log the detailed response to a file.
// file_put_contents(__DIR__ . '/../logs/zkteco_log.txt', date('Y-m-d H:i:s') . " --- \n" . $response_message . "\n\n", FILE_APPEND);
?>
