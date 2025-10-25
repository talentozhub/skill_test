<?php
require_once __DIR__ . '/../repository/EmployeeRepository.php';
require_once __DIR__ . '/../models/Employee.php';

class EmployeeService {
    private $repository;

    public function __construct(EmployeeRepository $repository) {
        $this->repository = $repository;
    }

    public function createEmployee($data) {
        $employee = new Employee();
        $employee->name = $data['name'];
        $employee->email = $data['email'];
        $employee->phoneNumber = $data['number'] ?? $data['phone_number'] ?? null;
        $employee->type = $data['type'] ?? $data['employee_type'] ?? null;
        $employee->gender = $data['gender'] ?? 'male';
        $employee->password = isset($data['password']) ? sha1($data['password']) : sha1(uniqid());
        $employee->email_sent = $data['email_sent'] ?? 0;
        $this->repository->save($employee);
        return $employee;
    }
}
