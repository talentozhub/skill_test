<?php

require_once ROOT . '/models/Employee.php';
require_once ROOT . '/src/Event/EventDispatcher.php';
require_once ROOT . '/src/Event/EmployeeCreatedEvent.php';
require_once ROOT . '/src/Event/WelcomeEmailListener.php';
require_once ROOT . '/src/Event/AuditLogListener.php';
require_once ROOT . '/src/Event/CsvReportListener.php';
require_once ROOT . '/src/Repository/EmployeeRepository.php';

class EmployeeService
{
    private $eventDispatcher;
    private $employeeRepository;

    public function __construct($eventDispatcher, $employeeRepository)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->employeeRepository = $employeeRepository;
    }

    public function createEmployee($data)
    {
        // 1. Validate data
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            throw new Exception('Name, email and password are required.');
        }

        // 2. Create and save employee
        $employee = new Employee();
        $employee->setName($data['name']);
        $employee->setEmail($data['email']);
        $employee->setPassword( sha1($data['password']));
        $employee->setPhoneNumber(isset($data['phone_number']) ? $data['phone_number'] : null);
        $employee->setType(isset($data['employee_type']) ? $data['employee_type'] : null);
        $employee->setGender(isset($data['gender']) ? $data['gender'] : null);

        $id = $this->employeeRepository->save($employee);

        $employee->setId($id);

        // Dispatch EmployeeCreatedEvent
        $this->eventDispatcher->dispatch(new EmployeeCreatedEvent($employee, $data['password']));

        return $employee;
    }
}
