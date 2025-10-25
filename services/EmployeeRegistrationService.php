<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../repository/EmployeeRepository.php';
require_once __DIR__ . '/../services/EmployeeService.php';
require_once __DIR__ . '/../services/EmployeeReportService.php';
require_once __DIR__ . '/../repository/AuditLogRepository.php';
require_once __DIR__ . '/../services/AuditLogService.php';

class EmployeeRegistrationService {
    private $pdo;
    private $employeeRepository;
    private $employeeService;
    private $auditRepo;
    private $auditService;

    public function __construct() {
        $this->pdo = Database::getConnection();
        $this->employeeRepository = new EmployeeRepository($this->pdo);
        $this->employeeService = new EmployeeService($this->employeeRepository);
        $this->auditRepo = new AuditLogRepository($this->pdo);
        $this->auditService = new AuditLogService($this->auditRepo);
    }

    /**
     * Registers a new employee, logs audit, sends email, appends report.
     * @param array $data
     * @param bool $sendMail
     * @return Employee
     */
    public function register($data, $sendMail = true) {
        $employee = $this->employeeService->createEmployee($data);
        $_SESSION["logged_in_user_id"] = $employee->id;
        $timestamp = date("d/m/y h:i:s");
        $msg = $employee->name . " was added on $timestamp";
        $this->auditService->log($msg);
        if ($sendMail) {
            $plainPassword = isset($data['password']) ? $data['password'] : 'generated';
            mail($employee->email, "Thanks for registering", "Dear " . $employee->name . ",\nThanks for registering with AwesomeCorp!! your password is $plainPassword.\nYou can login at: http://www.awesomecorp.com/login.\nRegards,\nAwesomeCorp");
        }
        $employee->email_sent = 1;
        $this->employeeRepository->save($employee);
        EmployeeReportService::appendToReport($employee);
        return $employee;
    }
}
