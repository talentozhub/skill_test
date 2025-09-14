<?php
session_start();

// Include simple database configuration
require_once __DIR__ . '/../config/database.php';

// Handle form submission first, before any HTML output
if ($_POST) {
    // Basic input sanitization
    $employeeName = trim($_POST['name'] ?? '');
    $phoneNumber = trim($_POST['phone_number'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $employeeType = $_POST['employee_type'] ?? '1';
    $gender = $_POST['gender'] ?? 'not_specified';
    $userPassword = $_POST['password'] ?? '';
    
    // Basic validation
    $errors = [];
    if (empty($employeeName)) $errors[] = "Employee name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (empty($userPassword)) $errors[] = "Password is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    
    // Display errors if any
    if (!empty($errors)) {
        echo "<h2>Validation Errors:</h2><ul>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</li>";
        }
        echo "</ul>";
        die();
    }
    
    try {
        // Get database connection
        $pdo = getDatabaseConnection();

        // Insert employee using prepared statements (already secure)
        $stmt = $pdo->prepare("INSERT INTO employee (`name`, `phone_number`, `email`, `type`, `gender`) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $employeeName,  // Using original data for database storage
            $phoneNumber,
            $email,
            $employeeType,
            $gender
        ]);

        $employeeId = $pdo->lastInsertId();

        // Add to audit log
        $timestamp = date("d/m/y h:i:s");
        $stmt = $pdo->prepare("INSERT INTO audit_log (`message`) VALUES (?)");
        $stmt->execute(["{$employeeName} was added on $timestamp"]);

        // Send email
        $emailBody = "Dear {$safeEmployeeName},\nThanks for registering with AwesomeCorp!! your password is {$userPassword}.\nYou can login at: http://www.awesomecorp.com/login.\nRegards,\nAwesomeCorp";
        mail($email, "Thanks for registering", $emailBody);

        // Update password and email_sent
        $hashedPassword = sha1($userPassword);
        $stmt = $pdo->prepare("UPDATE employee SET password = ?, email_sent = 1 WHERE id = ?");
        $stmt->execute([$hashedPassword, $employeeId]);

        // Add to CSV report
        $csvLine = "{$employeeId},{$employeeName},{$email}\n";
        file_put_contents('/tmp/employee_report.csv', $csvLine, FILE_APPEND | LOCK_EX);

        $_SESSION["logged_in_user_id"] = $employeeId;
        $newURL = "dashboard.php";
        header('Location: ' . $newURL);
        exit;
    } catch (PDOException $e) {
        die("<h2>Sorry could not add employee: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</h2>");
    }
}
?>

<h1>Create New Employee</h1>
<form action="" method="post">
    <ul>
        <li>
            Employee Name:
            <input name="name" type="text" required />
        </li>
        <li>
            Phone Number:
            <input name="phone_number" type="text" required />
        </li>
        <li>
            Password:
            <input name="password" type="password" required />
        </li>
        <li>
            Email:
            <input name="email" type="email" required />
        </li>
        <li>
            Employee Type:
            <select name="employee_type" required>
                <option value="1">Part Time</option>
                <option value="2">Full Time</option>
            </select>
        </li>
        <li>
            Gender:
            <select name="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="not_specified">Prefer not to specify</option>
            </select>
        </li>
    </ul>
    <input type="submit" value="Create">
</form>



