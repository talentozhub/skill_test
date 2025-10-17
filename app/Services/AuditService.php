<?php

namespace App\Services;

use App\Utils\Database;

class AuditService
{
    public function log($message)
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO audit_log (`message`) VALUES (:message)");
        $stmt->execute(array('message' => $message));
    }
}
