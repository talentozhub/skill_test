<?php
require_once __DIR__ . '/../services/EmployeeRegistrationService.php';
session_start();

if ($_POST) {
    try {
    $data = [
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'phone_number' => $_POST['phone_number'],
        'type' => $_POST['employee_type'],
        'gender' => $_POST['gender'],
        'password' => $_POST['password'],
        'email_sent' => 0
    ];
    $registrationService = new EmployeeRegistrationService();
    $employee = $registrationService->register($data);
    header('Location: dashboard.php');
    exit();
    } catch (Exception $e) {
        die("<h2>Sorry could not add employee: " . $e->getMessage(). "</h2>" );
    }
}
?>

<h1>Create New Employee</h1>
<form action="" method="post">
    <ul>
        <li>
            Employee Name:
            <input name="name" type="text" />
        </li>
        <li>
            Phone Number:
            <input name="phone_number" type="text"/>
        </li>
        <li>
            Password:
            <input name="password" type="password" />
        </li>
        <li>
            Email:
            <input name="email" type="text"/>
        </li>
        <li>
            Gender:
            <select name="gender">
                <option value="male" selected>Male</option>
                <option value="female">Female</option>
            </select>
        </li>
        <li>
            Employee Type:
            <select name="employee_type">
                <option value="1">Part Time</option>
                <option value="2">Full Time</option>
            </select>
        </li>
    </ul>
    <input type="submit" value="Create">
</form>
