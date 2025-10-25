<?php
require_once __DIR__ . '/../models/Employee.php';
class EmployeeRepository {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function save(Employee $employee) {
        if (!$employee->id) {
            $sql = $this->pdo->prepare("INSERT INTO employee (`name`, `phone_number`, `gender`, `type`, `email`, `password`, `email_sent`) VALUES (:name, :phone, :gender, :type, :email, :password, :email_sent)");
        } else {
            $sql = $this->pdo->prepare("UPDATE employee SET `name` = :name, `phone_number` = :phone, `gender` = :gender, `type` = :type, `email` = :email, `password` = :password, `email_sent` = :email_sent WHERE id = :id");
            $sql->bindParam(":id", $employee->id);
        }
        $sql->bindParam(':name', $employee->name);
        $sql->bindParam(':phone', $employee->phoneNumber);
        $sql->bindParam(':gender', $employee->gender);
        $sql->bindParam(':type', $employee->type);
        $sql->bindParam(':email', $employee->email);
        $sql->bindParam(':password', $employee->password);
        $sql->bindParam(':email_sent', $employee->email_sent);
        if (!$sql->execute()) {
            throw new Exception("Could not save employee.");
        }
        if (!$employee->id) {
            $employee->id = $this->pdo->lastInsertId();
        }
    }
    // Additional repository methods (find, delete, etc.) can be added here
}
