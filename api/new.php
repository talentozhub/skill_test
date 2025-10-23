<?php

require_once __DIR__ . '/../config/bootstrap.php';
require_once ROOT . '/src/Event/EventDispatcher.php';
require_once ROOT . '/src/Event/WelcomeEmailListener.php';
require_once ROOT . '/src/Event/AuditLogListener.php';
require_once ROOT . '/src/Event/CsvReportListener.php';
require_once ROOT . '/src/Repository/EmployeeRepository.php';
require_once ROOT . '/src/Service/EmployeeService.php';

loadEnv(__DIR__ . '/../.env'); // Explicitly load .env for direct script execution

function handle_API_Request($data)
{
    $db = require ROOT . '/config/database.php';

    $eventDispatcher = new EventDispatcher();
    $eventDispatcher->addListener('EmployeeCreatedEvent', array(new WelcomeEmailListener(), 'handle'));
    $eventDispatcher->addListener('EmployeeCreatedEvent', array(new AuditLogListener($db), 'handle'));
    $eventDispatcher->addListener('EmployeeCreatedEvent', array(new CsvReportListener(), 'handle'));

    $employeeRepository = new EmployeeRepository($db);
    $employeeService = new EmployeeService(
        $eventDispatcher,
        $employeeRepository
    );

    try {
        $employee = $employeeService->createEmployee($data);

        return array(
            'status' => 'success',
            'message' => 'Employee ' . $employee->getName() . ' (' . $employee->getId() . ') created successfully.'
        );
    } catch (Exception $e) {
        return array(
            'status' => 'error',
            'message' => 'Error creating employee: ' . $e->getMessage()
        );
    }
}

// Example usage:
$receivedData = array(
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'phone_number' => time(),
    'password' => 'as44dasd',
    'employee_type' => 1,
    'gender' => 1
);

header('Content-Type: application/json');
echo json_encode(handle_API_Request($receivedData));
