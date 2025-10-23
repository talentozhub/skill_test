<?php

require_once ROOT . '/models/Employee.php';
require_once ROOT . '/src/Event/EmployeeCreatedEvent.php';

class CsvReportListener
{
    public function handle($event)
    {
        $employee = $event->getEmployee();
        $csvPath = '/tmp/employee_report.csv';

        // Create the file if it doesn\'t exist and add headers
        if (!file_exists($csvPath)) {
            $handle = fopen($csvPath, 'w');
            if ($handle === false) {
                throw new Exception('Could not open CSV file for writing.');
            }
            fputcsv($handle, array('ID', 'Name', 'Email'));
            fclose($handle);
        }

        $handle = fopen($csvPath, 'a');
        if ($handle === false) {
            throw new Exception('Could not open CSV file for writing.');
        }

        fputcsv($handle, array(
            $employee->getId(),
            $employee->getName(),
            $employee->getEmail()
        ));

        fclose($handle);
    }
}
