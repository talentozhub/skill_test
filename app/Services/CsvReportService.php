<?php
// app/Services/CsvReportService.php
namespace App\Services;

use App\Models\Employee;

class CsvReportService
{
    protected $file;

    public function __construct()
    {
        $config = require __DIR__ . '/../config.php';
        $this->file = $config['csv_file'];
    }

    /**
     * Append newly created employee as CSV line
     *
     * Format: [employee id],[employee name],[employee email]
     *
     * @param Employee $employee
     */
    public function appendEmployee(Employee $employee)
    {
        $fp = fopen($this->file, 'a');
        if ($fp) {
            fputcsv($fp, array($employee->id, $employee->name, $employee->email));
            fclose($fp);
        }
    }
}
