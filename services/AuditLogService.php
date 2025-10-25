<?php
require_once __DIR__ . '/../repository/AuditLogRepository.php';
class AuditLogService {
    private $repository;
    public function __construct(AuditLogRepository $repository) {
        $this->repository = $repository;
    }
    public function log($message) {
    $log = new AuditLog();
    $log->message = $message;
    $this->repository->add($log);
    }
}
