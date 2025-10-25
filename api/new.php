<?php

require_once("../services/EmployeeRegistrationService.php");
require_once("../services/EmployeeReportService.php");

/**
 * For the purposes of the test, the way that the data comes in to the system is not relevant. Let's assume that
 * we have a REST API, and this is a POST request to the "new employee" endpoint. The data provided is the array
 * that has been specified here.
 */

//allowed data is "name", "number", "email" & "type"
$receivedData = array(
    "name" => "Bob Smith",
    "email" => "luke.zawadzki@astutepayroll.com",
    "number" => "+61 430 131 409",
    "type" => "full-time",
    "gender" => "male"
);

//this simulates a call to the API.
var_dump(handle_API_Request($receivedData));

/**
 * This is the processing code for the API.
 */
function handle_API_Request($data) {
    try {
        $registrationService = new EmployeeRegistrationService();
        $employee = $registrationService->register($data, false); // false: API mail template is different
        // API-specific mail
        $plainPassword = isset($data['password']) ? $data['password'] : 'generated';
        mail($employee->email, "Thanks for registering", "Dear " . $employee->name . ",\nYou have been added to AwesomeCorp! your password is $plainPassword.\nYou can login at: http://awesomecorp.recruiterforce.com/login.\nRegards,\nRecruiterForce");
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
