<?php
namespace App\Services;

use App\Models\Employee;
use App\Utils\Validator;
use App\Repositories\EmployeeRepository;

class EmployeeService
{
    private $auditService;
    private $csvService;
    private $mailService;
    private $employeeRepo;

    public function __construct()
    {
        $this->auditService = new AuditService();
        $this->csvService = new CsvReportService();
        $this->mailService = new MailService();
        $this->employeeRepo = new EmployeeRepository();
    }

    public function createEmployee($data)
    {
        $this->validateEmployeeData($data);

        if ($this->employeeRepo->isDuplicate($data['email'], $data['phone_number'])) {
            throw new \InvalidArgumentException('Email or Phone number already exists', 409);
        }

        $employee = new Employee();
        $employee->name = $data['name'];
        $employee->email = $data['email'];
        $employee->phone_number = $data['phone_number'];
        $employee->type = $data['type'];
        $employee->gender = isset($data['gender']) ? $data['gender'] : null;

        $plainPassword = isset($data['password']) ? $data['password'] : uniqid();
        $employee->password = password_hash($plainPassword, PASSWORD_DEFAULT);

        $this->employeeRepo->save($employee);

        $this->auditService->log($employee->name . " was added on " . date("d/m/y H:i:s"));
        $this->mailService->sendEmployeeWelcome($employee, $plainPassword); // TODO: handle failure case and don't update email_sent if mail fails.
        $this->employeeRepo->updateEmailSent($employee, 1);
        $this->csvService->appendEmployee($employee);

        return $employee;
    }

    private function validateEmployeeData($data)
    {
        Validator::required($data, ['name', 'email', 'phone_number', 'type']);
        if (!Validator::email($data['email'])) throw new \InvalidArgumentException('Invalid email format', 400);
        if (!Validator::phone($data['phone_number'])) throw new \InvalidArgumentException('Invalid phone number', 400);
        if (!Validator::inArray($data['type'], [Employee::TYPE_FULL_TIME, Employee::TYPE_PART_TIME])) {
            throw new \InvalidArgumentException('Invalid employee type', 400);
        }
        if (isset($data['gender']) && !Validator::inArray($data['gender'], [Employee::GENDER_MALE, Employee::GENDER_FEMALE])) {
            throw new \InvalidArgumentException('Invalid gender value', 400);
        }
    }
}
