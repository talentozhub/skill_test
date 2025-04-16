<?php
session_start();

function getDbConnection() {
    $conn = new mysqli('localhost', 'root', '', 'test');
    if ($conn->connect_error) {
        die("<h2>DB Connection Failed: " . $conn->connect_error . "</h2>");
    }
    return $conn;
}

function escape($conn, $val) {
    return $conn->real_escape_string($val);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDbConnection();

    $name = escape($conn, $_POST['name']);
    $phone = escape($conn, $_POST['phone_number']);
    $email = escape($conn, $_POST['email']);
    $type = escape($conn, $_POST['employee_type']);
    $gender = escape($conn, $_POST['gender']);
    $password = $_POST['password'];
    $hashedPassword = sha1($password);

    // Insert employee
    $stmt = $conn->prepare("INSERT INTO employee (name, phone_number, email, type, gender, password, email_sent) VALUES (?, ?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("ssssss", $name, $phone, $email, $type, $gender, $hashedPassword);
    $success = $stmt->execute();

    if (!$success) {
        die("<h2>Sorry, could not add employee: " . $stmt->error . "</h2>");
    }

    $employeeId = $stmt->insert_id;

    // Send email
    $message = "Dear $name,\nThanks for registering with AwesomeCorp!! Your password is $password.\nYou can login at: http://www.awesomecorp.com/login.\nRegards,\nAwesomeCorp";
    mail($email, "Thanks for registering", $message);

    // Log audit
    $timestamp = date("d/m/y h:i:s");
    $logMessage = "$name was added on $timestamp";
    $stmt = $conn->prepare("INSERT INTO audit_log (message) VALUES (?)");
    $stmt->bind_param("s", $logMessage);
    $stmt->execute();

    // Append to CSV
    $csvLine = "$employeeId,$name,$email\n";
    file_put_contents("/tmp/employee_report.csv", $csvLine, FILE_APPEND); 
    // file_put_contents(__DIR__ . "/employee_report.csv", $csvLine, FILE_APPEND); // use this if there's any permission issue

    // Session + Redirect
    $_SESSION['logged_in_user_id'] = $employeeId;
    header('Location: dashboard.php');
    exit;
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
                <option value="">Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </li>
    </ul>
    <input type="submit" value="Create">
</form>
