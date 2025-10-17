<?php
namespace App\Services;

use App\Models\Employee;

class MailService
{
    public function sendEmployeeWelcome(Employee $employee, $plainPassword)
    {
        $subject = "Thanks for registering";
        $body = "Dear " . $employee->name . ",\n\nYou have been added to AwesomeCorp! Your password is " 
                . $plainPassword 
                . ".\nYou can login at: http://awesomecorp.recruiterforce.com/login.\n\nRegards,\nRecruiterForce";
        try {
            $success = @mail($employee->email, $subject, $body);
            return $success;
        } catch (\Exception $e) {
            // TODO: log here for exception
            return false;
        }
    }
}
