<?php

class EmployeeRepository
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function save($employee)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO employee (name, email, type, phone_number, gender, password) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute(array(
            $employee->getName(),
            $employee->getEmail(),
            $employee->getType(),
            $employee->getPhoneNumber(),
            $employee->getGender(),
            $employee->getPassword()
        ));

        return $this->db->lastInsertId();
    }
}
