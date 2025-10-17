<?php
namespace App\Repositories;

use App\Models\Employee;
use App\Utils\Database;
use PDO;
use Exception;

class EmployeeRepository
{

    protected $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function save(Employee $employee)
    {
        $pdo = Database::getConnection();

        if ($employee->id === null) {
            $stmt = $this->pdo->prepare(
                "INSERT INTO employee (`name`, `phone_number`, `type`, `email`, `password`, `email_sent`, `gender`)
                 VALUES (:name, :phone, :type, :email, :password, :email_sent, :gender)"
            );
        } else {
            $stmt = $this->pdo->prepare(
                "UPDATE employee SET `name`=:name, `phone_number`=:phone, `type`=:type, `email`=:email, `password`=:password, `email_sent`=:email_sent, `gender`=:gender WHERE id = :id"
            );
            $stmt->bindParam(':id', $employee->id);
        }

        $stmt->bindParam(':name', $employee->name);
        $stmt->bindParam(':phone', $employee->phone_number);
        $stmt->bindParam(':type', $employee->type);
        $stmt->bindParam(':email', $employee->email);
        $stmt->bindParam(':password', $employee->password);
        $stmt->bindParam(':email_sent', $employee->email_sent);
        $stmt->bindParam(':gender', $employee->gender);

        if (!$stmt->execute()) {
            throw new Exception("Could not save employee.");
        }

        if ($employee->id === null) {
            $employee->id = (int) $this->pdo->lastInsertId();
        }

        return $employee->id;
    }

    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM employee WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) return null;

        $employee = new Employee();
        $employee->id = $data['id'];
        $employee->name = $data['name'];
        $employee->phone_number = $data['phone_number'];
        $employee->type = $data['type'];
        $employee->email = $data['email'];
        $employee->password = $data['password'];
        $employee->email_sent = $data['email_sent'];
        $employee->gender = $data['gender'] ?? null;

        return $employee;
    }

    public function isDuplicate($email, $phone)
    {
        $pdo = Database::getConnection();
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM employee WHERE email=:email OR phone_number=:phone");
        $stmt->execute(array(':email'=>$email, ':phone'=>$phone));
        return $stmt->fetchColumn() > 0;
    }

    public function updateEmailSent(Employee $employee, $sent = 1)
    {
        $pdo = Database::getConnection();
        $stmt = $this->pdo->prepare("UPDATE employee SET email_sent = :sent WHERE id = :id");
        $stmt->bindParam(':sent', $sent, PDO::PARAM_INT);
        $stmt->bindParam(':id', $employee->id, PDO::PARAM_INT);
        $stmt->execute();

        $employee->email_sent = $sent;
    }
}
