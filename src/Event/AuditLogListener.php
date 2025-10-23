<?php

require_once ROOT . '/models/Employee.php';
require_once ROOT . '/src/Event/EmployeeCreatedEvent.php';
require_once ROOT . '/models/AuditLog.php';
require_once ROOT . '/src/Repository/AuditLogRepository.php';

class AuditLogListener
{
    private $auditLogRepository;

    public function __construct($db)
    {
        $this->auditLogRepository = new AuditLogRepository($db);
    }

    public function handle($event)
    {
        $employee = $event->getEmployee();
        $message = $employee->getName() . " was added on " . date("d/m/y h:i:s");
        
        $auditLog = new AuditLog();
        $auditLog->setMessage($message);
        $this->auditLogRepository->save($auditLog);
    }
}
