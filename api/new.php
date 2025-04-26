<?php

require_once("../models/Employee.php");

/**
 * For the purposes of the test, the way that the data comes in to the system is not relevant. Let's assume that
 * we have a REST API, and this is a POST request to the "new employee" endpoint. The data provided is the array
 * that has been specified here.
 */

//allowed data is "name", "number", "email" & "type"
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$mobile = $_POST['mobile'] ?? '';
$gender = $_POST['gender'] ?? '';
$type = $_POST['type'] ?? '';

$receivedData = array(
    "name" => $name,
    "email" => $email,
    "number" => $mobile,
    "type" => $type,
    "gender" => $gender
);

//this simulates a call to the API.
var_dump(handle_API_Request($receivedData));



/**
 * This is the processing code for the API.
 */
function handle_API_Request($data) {
    try {

        $connection = mysqli_connect("localhost", "root", "", "test");
        if (!$connection) {
            throw new Exception('Database connection failed: ' . mysqli_connect_error());
        }

        $employee = new Employee();
        $employee->name = $data['name'];
        $employee->email = $data['email'];
        $employee->phoneNumber = $data['number'];
        $employee->type = $data['type'];
        $employee->gender = $data['gender'];
        $generatedPassword = uniqid();
        $employee->password = sha1($generatedPassword);

        $employee->save();

        //auditing
        $timestamp = date("d/m/y h:i:s");
        $auditSql = "INSERT INTO audit_log (`message`) VALUES (?)";
        $stmt = mysqli_prepare($connection, $auditSql);
        mysqli_stmt_bind_param($stmt, 's', $message);
        $message = "{$employee->name} was added on $timestamp";
        mysqli_stmt_execute($stmt);

        // ✨ Append to CSV file
        $csvLine = "{$employee->name}\t{$employee->email}\t{$employee->phoneNumber}\t{$employee->type}\t{$employee->gender}\n";
        file_put_contents(__DIR__ . '/../tmp/employee_report.csv', $csvLine, FILE_APPEND | LOCK_EX);


        // Send comms
        mail(
            $employee->email,
            "Thanks for registering",
            "Dear " . $employee->name . ",\nYou have been added to AwesomeCorp! Your password is $generatedPassword.\nYou can login at: http://awesomecorp.recruiterforce.com/login.\nRegards,\nRecruiterForce"
        );

        $employee->email_sent = 1;
        $employee->save();

        return array(
            "status" => "success",
            "message" => $employee->id . " was added"
        );
    }
    catch (exception $e) {
        return array(
            "status" => "error",
            "message" => $e->getMessage()
        );
    }

}
