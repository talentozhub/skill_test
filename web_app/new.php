<?php

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    require_once __DIR__ . '/../config/bootstrap.php';
    $db = require ROOT . '/config/database.php';
    require_once ROOT . '/src/Event/EventDispatcher.php';
    require_once ROOT . '/src/Event/WelcomeEmailListener.php';
    require_once ROOT . '/src/Event/AuditLogListener.php';
    require_once ROOT . '/src/Event/CsvReportListener.php';
    require_once ROOT . '/src/Repository/EmployeeRepository.php';
    require_once ROOT . '/src/Service/EmployeeService.php';

    $eventDispatcher = new EventDispatcher();
    $eventDispatcher->addListener('EmployeeCreatedEvent', array(new WelcomeEmailListener(), 'handle'));
    $eventDispatcher->addListener('EmployeeCreatedEvent', array(new AuditLogListener($db), 'handle'));
    $eventDispatcher->addListener('EmployeeCreatedEvent', array(new CsvReportListener(), 'handle'));

    $employeeRepository = new EmployeeRepository($db);
    $employeeService = new EmployeeService(
        $eventDispatcher,
        $employeeRepository
    );

    try {
        $employee = $employeeService->createEmployee($_POST);
        $message = 'Employee created successfully!';
        $_SESSION["logged_in_user_id"] = $employee->getId();

        $newURL = "dashboard.php";
        header('Location: ' . $newURL);
        exit;
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Employee</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        button:hover {
            background-color: #218838;
        }

        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Create New Employee</h1>
        <?php if (isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <input type="text" id="phone_number" name="phone_number" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender">
                    <option value="1">Male</option>
                    <option value="2">Female</option>
                </select>
            </div>
            <div class="form-group">
                <label for="employee_type">Employee Type:</label>
                <select id="employee_type" name="employee_type">
                    <option value="1">Part Time</option>
                    <option value="2">Full Time</option>
                </select>
            </div>
            <button type="submit">Create Employee</button>
        </form>
    </div>
</body>

</html>