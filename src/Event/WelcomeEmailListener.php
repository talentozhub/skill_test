<?php

require_once ROOT . '/models/Employee.php';
require_once ROOT . '/src/Event/EmployeeCreatedEvent.php';

class WelcomeEmailListener
{
    public function handle($event)
    {
        $employee = $event->getEmployee();

        $uniq = $event->getPlainPassword();

        mail($employee->getEmail(), "Thanks for registering", "Dear " . $employee->getName() .
         ",\nThanks for registering with AwesomeCorp!! your password is $uniq.\nYou can login at: http://www.awesomecorp.com/login.\nRegards,\nAwesomeCorp");

    }
}
