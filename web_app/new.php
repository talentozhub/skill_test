<?php
session_start();
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
                <option value="part-time">Part Time</option>
                <option value="full-time">Full Time</option>
            </select>
        </li>
        <li>
            Gender:
            <label><input type="radio" name="gender" value="Male" required> Male</label>
            <label><input type="radio" name="gender" value="Female" required> Female</label>
        </li>
    </ul>
    <input type="submit" value="Create">
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $connection = mysqli_connect("localhost", "root", "", "test");
    if (!$connection) {
        die("<h2>Database connection failed: " . mysqli_connect_error() . "</h2>");
    }

    $name = mysqli_real_escape_string($connection, $_POST['name']);
    $phone = mysqli_real_escape_string($connection, $_POST['phone_number']);
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $type = mysqli_real_escape_string($connection, $_POST['employee_type']);
    $gender = mysqli_real_escape_string($connection, $_POST['gender']);
    $passwordPlain = $_POST['password'];
    $passwordHashed = sha1($passwordPlain);

    $stmt = mysqli_prepare($connection, "INSERT INTO employee (`name`, `phone_number`, `email`, `type`, `gender`) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'sssss', $name, $phone, $email, $type,$gender);
    $result = mysqli_stmt_execute($stmt);

    if (!$result) {
        die("<h2>Sorry, could not add employee: " . mysqli_error($connection) . "</h2>");
    }

    $timestamp = date("d/m/y h:i:s");
    $auditMessage = "$name was added on $timestamp";
    $auditStmt = mysqli_prepare($connection, "INSERT INTO audit_log (`message`) VALUES (?)");
    mysqli_stmt_bind_param($auditStmt, 's', $auditMessage);
    mysqli_stmt_execute($auditStmt);

    // ✨ Append to CSV file
    $csvLine = "{$name}\t{$email}\t{$phone}\t{$type}\t{$gender}\n";
    file_put_contents(__DIR__ . '/../tmp/employee_report.csv', $csvLine, FILE_APPEND | LOCK_EX);



    // Send email
    mail(
        $email,
        "Thanks for registering",
        "Dear $name,\nThanks for registering with AwesomeCorp!! Your password is $passwordPlain.\nYou can login at: http://www.awesomecorp.com/login.\nRegards,\nAwesomeCorp"
    );

    // Get the last inserted ID
    $last_id = mysqli_insert_id($connection);

    $_SESSION['logged_in_user_id'] = $last_id;

    $updateStmt = mysqli_prepare($connection, "UPDATE employee SET `password` = ?, `email_sent` = 1 WHERE id = ?");
    mysqli_stmt_bind_param($updateStmt, 'si', $passwordHashed, $last_id);
    mysqli_stmt_execute($updateStmt);

    header('Location: dashboard.php');
    exit;
}
?>
