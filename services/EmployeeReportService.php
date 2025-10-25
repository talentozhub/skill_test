<?php
class EmployeeReportService {
    public static function appendToReport($employee) {
        $csvLine = $employee->id . ',' . $employee->name . ',' . $employee->email . "\n";
        file_put_contents('/tmp/employee_report.csv', $csvLine, FILE_APPEND | LOCK_EX);
    }
}
