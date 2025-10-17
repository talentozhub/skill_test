<?php
// public/web_app/dashboard.php
require_once __DIR__ . '/../../app/bootstrap.php';

use App\Repositories\EmployeeRepository;

session_start();
if (!isset($_SESSION['logged_in_user_id'])) {
    header('Location: new.php');
    exit;
}

$repo = new EmployeeRepository();
$employee = $repo->findById($_SESSION['logged_in_user_id']);

if (!$employee) {
    echo "Employee not found!";
    exit;
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Dashboard</title></head>
<body>
<h1>Welcome, <?= htmlspecialchars($employee->name) ?>!</h1>
<p>Email: <?= htmlspecialchars($employee->email) ?></p>
<p>Phone: <?= htmlspecialchars($employee->phone_number) ?></p>
<p>Type: <?= htmlspecialchars($employee->type) ?></p>
<p>Gender: <?= htmlspecialchars($employee->gender) ?></p>
<hr>
<p>Your Employee ID: <?= htmlspecialchars($employee->id) ?></p>
<p><a href="logout.php">Logout</a></p>
</body>
</html>
