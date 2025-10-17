<?php
require_once(__DIR__ . "/../models/EmployeeService.php");

$receivedData = array(
    "name" => "Bob Smith",
    "email" => "luke.zawadzki@astutepayroll.com",
    "number" => "+61 430 131 409",
    "type" => "full-time",
    "gender" => "male"
);

header('Content-Type: application/json');
echo json_encode(handle_API_Request($receivedData));

function handle_API_Request($data) {
    $result = EmployeeService::createEmployee($data, $generatePassword = true);
    return $result;
}