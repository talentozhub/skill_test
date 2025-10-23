<?php

require_once ROOT . '/models/AuditLog.php';

class AuditLogRepository
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function save($auditLog)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO audit_log (message) VALUES (?)'
        );
        $stmt->execute(array(
            $auditLog->getMessage(),
        ));

        return $this->db->lastInsertId();
    }
}
