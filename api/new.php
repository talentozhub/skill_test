<?php

require_once("../models/Employee.php");

/**
 * For the purposes of the test, the way that the data comes in to the system is not relevant. Let's assume that
 * we have a REST API, and this is a POST request to the "new employee" endpoint. The data provided is the array
 * that has been specified here.
 */
$receivedData = array(
    "name" => "Bob Smith",
    "email" => "luke.zawadzki@astutepayroll.com",
    "number" => "+61 430 131 409",
    "type" => "full-time",
    "gender" => "male"
);

// Simulate API request handling
var_dump(handle_API_Request($receivedData));

/**
 * Main API handler
 */
function handle_API_Request($data) {
    try {
        // Validate input
        $requiredFields = ['name', 'email', 'number', 'type', 'gender'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return [
                    "status" => "error",
                    "message" => "$field is required"
                ];
            }
        }

        // Connect to MySQL
        $conn = new mysqli("localhost", "root", "", "test");
        if ($conn->connect_error) {
            throw new Exception("Database connection failed: " . $conn->connect_error);
        }

        // Prepare employee data
        $employee = new Employee();
        $employee->name = $data['name'];
        $employee->email = $data['email'];
        $employee->phoneNumber = $data['number'];
        $employee->type = $data['type'];
        $typeMap = [
            "full-time" => 1,
            "part-time" => 2
        ];
        
        $typeValue = isset($data['type'], $typeMap[$data['type']]) ? $typeMap[$data['type']] : null;
        
        if ($typeValue === null) {
            throw new Exception("Invalid employee type");
        }
        
        $employee->type = $typeValue;
        $generatedPassword = uniqid();
        $employee->password = sha1($generatedPassword);
        $employee->email_sent = 0;
        $employee->gender =$data['gender'];

        $employee->save();

        // Log audit
        $timestamp = date("d/m/y h:i:s");
        $message = "{$employee->name} was added on $timestamp";
        $stmt = $conn->prepare("INSERT INTO audit_log (`message`) VALUES (?)");
        $stmt->bind_param("s", $message);
        $stmt->execute();

        // Send email
        $mailBody = "Dear {$employee->name},\nYou have been added to AwesomeCorp! Your password is $generatedPassword.\nYou can login at: http://awesomecorp.recruiterforce.com/login.\nRegards,\nRecruiterForce";
        mail($employee->email, "Thanks for registering", $mailBody);

        // Update email_sent
        $employee->email_sent = 1;
        $employee->save();

        // Append to CSV
        $csvLine = "{$employee->id},{$employee->name},{$employee->email},{$employee->gender}\n";
        file_put_contents("/tmp/employee_report.csv", $csvLine, FILE_APPEND); // Change path if needed

        return array(
            "status" => "success",
            "message" => $employee->id . " was added"
        );
    } catch (Exception $e) {
        return array(
            "status" => "error",
            "message" => $e->getMessage()
        );
    }
}
