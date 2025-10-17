<?php

return array(
    'db_host' => getenv('DB_HOST') ?: 'db',
    'db_name' => getenv('DB_NAME') ?: 'test',
    'db_user' => getenv('DB_USER') ?: 'root',
    'db_pass' => getenv('DB_PASS') ?: 'root',
    'csv_file' => '/tmp/employee_report.csv'
);
