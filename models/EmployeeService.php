<?php
require_once(__DIR__ . '/Employee.php');
require_once(__DIR__ . '/../config.php');

class EmployeeService {

    public static function createEmployee($data, $generatePassword = false) {
        try {
            // Validate required fields
            self::validateEmployeeData($data);

            $employee = new Employee();
            $employee->name = $data['name'];
            $employee->email = $data['email'];
            $employee->phoneNumber = isset($data['number']) ? $data['number'] : $data['phone_number'];
            $employee->type = $data['type'];
            $employee->gender = isset($data['gender']) ? $data['gender'] : null;

            // Handle password
            if ($generatePassword) {
                $generatedPassword = self::generatePassword();
                $employee->password = sha1($generatedPassword);
            } else {
                $generatedPassword = $data['password'];
                $employee->password = sha1($generatedPassword);
            }

            // Save employee to database
            $employee->save();

            // Log audit entry
            self::logAudit($employee->name);

            // Add to CSV report
            self::addToReport($employee);

            // Send welcome email
            self::sendWelcomeEmail($employee, $generatedPassword);

            // Mark email as sent
            $employee->email_sent = 1;
            $employee->save();

            return array(
                "status" => "success",
                "message" => $employee->id . " was added",
                "employee_id" => $employee->id
            );
        } catch (Exception $e) {
            return array(
                "status" => "error",
                "message" => $e->getMessage()
            );
        }
    }

    /**
     * Validate employee data
     *
     * @param array $data
     * @throws Exception
     */
    private static function validateEmployeeData($data) {
        $required = array('name', 'email', 'type');
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }

        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Validate employee type
        if (!in_array($data['type'], array(Employee::TYPE_FULL_TIME, Employee::TYPE_PART_TIME))) {
            throw new Exception("Invalid employee type");
        }

        // Validate gender if provided
        if (isset($data['gender']) && !empty($data['gender'])) {
            if (!in_array($data['gender'], array(Employee::GENDER_MALE, Employee::GENDER_FEMALE))) {
                throw new Exception("Invalid gender value");
            }
        }
    }

    /**
     * Generate a random password
     *
     * @return string
     */
    private static function generatePassword() {
        return bin2hex(random_bytes(8));
    }

    /**
     * Log audit entry
     *
     * @param string $employeeName
     * @throws Exception
     */
    private static function logAudit($employeeName) {
        $pdo = getDBConnection();
        $timestamp = date("Y-m-d H:i:s");
        $message = $employeeName . " was added on " . $timestamp;

        $stmt = $pdo->prepare("INSERT INTO audit_log (`message`) VALUES (:message)");
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);

        if (!$stmt->execute()) {
            throw new Exception("Could not log audit entry");
        }
    }

    /**
     * Add employee to CSV report
     *
     * @param Employee $employee
     * @throws Exception
     */
    private static function addToReport($employee) {
        $reportPath = REPORTS_PATH . '/employee_report.csv';
        
        $employeeData = $employee->toArray();
        $line = implode(',', array_map(array(__CLASS__, 'escapeCsvField'), $employeeData));

        if (file_put_contents($reportPath, $line . "\n", FILE_APPEND | LOCK_EX) === false) {
            throw new Exception("Could not write to employee report file");
        }
    }

    /**
     * Escape CSV field
     *
     * @param string $field
     * @return string
     */
    private static function escapeCsvField($field) {
        if (preg_match('/[",\n\r]/', $field)) {
            return '"' . str_replace('"', '""', $field) . '"';
        }
        return $field;
    }

    /**
     * Send welcome email to employee
     *
     * @param Employee $employee
     * @param string $password
     */
    private static function sendWelcomeEmail($employee, $password) {
        $subject = "Thanks for registering";
        $body = "Dear " . $employee->name . ",\n\n";
        $body .= "You have been added to AwesomeCorp!\n";
        $body .= "Your password is: " . $password . "\n\n";
        $body .= "You can login at: http://awesomecorp.recruiterforce.com/login\n\n";
        $body .= "Regards,\n";
        $body .= "AwesomeCorp\n";

        mail($employee->email, $subject, $body);
    }
}