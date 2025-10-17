<?php
// public/api/new.php
require_once __DIR__ . '/../../app/bootstrap.php';

use App\Services\EmployeeService;

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$data = $input ? $input : $_POST;

//Testing data or hardcoded data if no input is provided
if (empty($data)) {
    $data = array(
        "name" => "Bob Smith",
        "email" => "luke.zawadzki@astutepayroll.com",
        "phone_number" => "+61 430 131 409",
        "type" => "full-time"
    );
}

try {
    $service = new EmployeeService();
    $data['generate_password'] = true;
    $employee = $service->createEmployee($data);
    echo json_encode(array('status' => 'success', 'message' => $employee->id . ' was added'));
} catch (Exception $e) {
    $code = !empty($e->getCode()) ? $e->getCode() : 500;
    http_response_code($code);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
