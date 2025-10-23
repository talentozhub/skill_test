<?php

require_once ROOT . '/models/Employee.php';

class EmployeeCreatedEvent
{
    private $employee;

    private $plainPassword;

    public function __construct($employee, $plainPassword)
    {
        $this->employee = $employee;
        $this->plainPassword = $plainPassword;
    }

    public function getEmployee()
    {
        return $this->employee;
    }

    public function getPlainPassword(){
        return $this->plainPassword;
    }
}
