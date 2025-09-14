<?php

require_once("../models/Employee.php");
require_once("../config/database.php");

/**
 * For the purposes of the test, the way that the data comes in to the system is not relevant. Let's assume that
 * we have a REST API, and this is a POST request to the "new employee" endpoint. The data provided is the array
 * that has been specified here.
 */

//allowed data is "name", "number", "email", "type" & "gender"
$receivedData = array(
    "name" => "Bob Smith",
    "email" => "luke.zawadzki@astutepayroll.com",
    "number" => "+61 430 131 409",
    "type" => "1",
    "gender" => "male"
);

//this simulates a call to the API.
var_dump(handle_API_Request($receivedData));

/**
 * This is the processing code for the API.
 */
function handle_API_Request($data) {
    // Basic input sanitization
    $employeeName = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $phoneNumber = trim($data['number'] ?? '');
    $employeeType = $data['type'] ?? '1';
    $gender = $data['gender'] ?? 'not_specified';
    
    // Basic validation
    if (empty($employeeName) || empty($email)) {
        return array(
            "status" => "error",
            "message" => "Name and email are required"
        );
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return array(
            "status" => "error",
            "message" => "Invalid email format"
        );
    }
    
    try {
        // Get database connection
        $pdo = getDatabaseConnection();

        $employee = new Employee();
        $employee->name = $employeeName;  // Using original data for database storage
        $employee->email = $email;
        $employee->phoneNumber = $phoneNumber;
        $employee->type = $employeeType;
        $employee->gender = $gender;
        $generatedPassword = uniqid();
        $employee->password = sha1($generatedPassword);

        $employee->save();

        //auditing
        $timestamp = date("d/m/y h:i:s");
        $stmt = $pdo->prepare("INSERT INTO audit_log (`message`) VALUES (?)");
        $stmt->execute(["{$employeeName} was added on $timestamp"]);

        //send comms
        $emailBody = "Dear {$employeeName},\nYou have been added to AwesomeCorp! your password is {$generatedPassword}.\nYou can login at: http://awesomecorp.recruiterforce.com/login.\nRegards,\nRecruiterForce";
        mail($email, "Thanks for registering", $emailBody);

        $employee->email_sent = 1;
        $employee->save();

        // Add to CSV report
        $csvLine = "{$employee->id},{$employeeName},{$email}\n";
        file_put_contents('/tmp/employee_report.csv', $csvLine, FILE_APPEND | LOCK_EX);

        return array(
            "status" => "success",
            "message" => $employee->id . " was added"
        );
    }
    catch (exception $e) {
        return array(
            "status" => "error",
            "message" => htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
        );
    }
}
