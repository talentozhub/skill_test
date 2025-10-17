<?php
session_start();
require_once(__DIR__ . "/../models/Employee.php");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; padding: 10px; background: #e0ffe0; border: 1px solid green; margin: 10px 0; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>Dashboard</h1>

    <?php if (isset($_SESSION['logged_in_user_id'])): ?>
        <div class="success">
            <strong>Welcome!</strong> Employee ID: <?php echo htmlspecialchars($_SESSION['logged_in_user_id']); ?> is logged in!
        </div>

        <?php
            try {
                $employee = Employee::getById($_SESSION['logged_in_user_id']);
                if ($employee):
        ?>
            <h2>Your Information</h2>
            <ul>
                <li><strong>Name:</strong> <?php echo htmlspecialchars($employee->name); ?></li>
                <li><strong>Email:</strong> <?php echo htmlspecialchars($employee->email); ?></li>
                <li><strong>Phone:</strong> <?php echo htmlspecialchars($employee->phoneNumber); ?></li>
                <li><strong>Type:</strong> <?php echo htmlspecialchars($employee->type); ?></li>
                <li><strong>Gender:</strong> <?php echo htmlspecialchars($employee->gender ?? 'Not specified'); ?></li>
            </ul>
        <?php
                endif;
            } catch (Exception $e) {
                echo "<p>Error loading employee data: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        ?>
    <?php else: ?>
        <p>You are not logged in. Please <a href="new.php">register here</a>.</p>
    <?php endif; ?>
</body>
</html>