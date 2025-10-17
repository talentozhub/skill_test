<?php

require_once __DIR__ . '/../../app/bootstrap.php';

use App\Services\EmployeeService;

session_start();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $service = new EmployeeService();
        $employee = $service->createEmployee($_POST);
        $_SESSION['logged_in_user_id'] = $employee->id;
        header('Location: dashboard.php');
        exit;
    } catch (Exception $e) {
        $code = !empty($e->getCode()) ? $e->getCode() : 500;
        http_response_code($code);
        $error = $e->getMessage();
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Create New Employee</title></head>
<body>
<h1>Create New Employee</h1>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post">
    <ul>
        <li>
            Employee Name:
            <input name="name" type="text" required />
        </li>
        <li>
            Phone Number:
            <input name="phone_number" type="text" required/>
        </li>
        <li>
            Email:
            <input name="email" type="email" required />
        </li>
        <li>
            Password:
            <input name="password" type="password" />
        </li>
        <li>
            Employee Type:
            <select name="type" required>
                <option value="full-time">Full Time</option>
                <option value="part-time">Part Time</option>
            </select>
        </li>
        <li>
            Gender:
            <select name="gender">
                <option value="">Select</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </li>
    </ul>
    <input type="submit" value="Create">
</form>
</body>
</html>
