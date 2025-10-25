<?php
require_once __DIR__ . '/../models/AuditLog.php';
class AuditLogRepository {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function add(AuditLog $log) {
    $sql = $this->pdo->prepare("INSERT INTO audit_log (`message`) VALUES (:message)");
    $sql->bindParam(":message", $log->message);
    $sql->execute();
    return $this->pdo->lastInsertId();
    }
}
