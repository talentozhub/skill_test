<?php
session_start();
require_once(__DIR__ . "/../models/EmployeeService.php");

$error = null;
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Prepare data from form
        $data = array(
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone_number' => $_POST['phone_number'] ?? '',
            'type' => $_POST['employee_type'] ?? '',
            'gender' => $_POST['gender'] ?? '',
            'password' => $_POST['password'] ?? ''
        );

        // Create employee using service
        $result = EmployeeService::createEmployee($data, $generatePassword = false);

        if ($result['status'] === 'success') {
            $success = true;
            $_SESSION['logged_in_user_id'] = $result['employee_id'];
            // Redirect to dashboard after 2 seconds
            header('refresh:2;url=dashboard.php');
        } else {
            $error = $result['message'];
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create New Employee</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .error { color: red; padding: 10px; background: #ffe0e0; border: 1px solid red; margin: 10px 0; }
        .success { color: green; padding: 10px; background: #e0ffe0; border: 1px solid green; margin: 10px 0; }
        .form-group { margin: 15px 0; }
        label { display: block; font-weight: bold; margin: 5px 0; }
        input, select { padding: 8px; width: 300px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        .required { color: red; }
    </style>
</head>
<body>
    <h1>Create New Employee</h1>

    <?php if ($error): ?>
        <div class="error">
            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success">
            <strong>Success!</strong> Employee created successfully. Redirecting to dashboard...
        </div>
    <?php endif; ?>

    <?php if (!$success): ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Employee Name <span class="required">*</span></label>
                <input type="text" id="name" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" />
            </div>

            <div class="form-group">
                <label for="email">Email <span class="required">*</span></label>
                <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
            </div>

            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : ''; ?>" />
            </div>

            <div class="form-group">
                <label for="password">Password <span class="required">*</span></label>
                <input type="password" id="password" name="password" required />
            </div>

            <div class="form-group">
                <label for="employee_type">Employee Type <span class="required">*</span></label>
                <select id="employee_type" name="employee_type" required>
                    <option value="">-- Select Type --</option>
                    <option value="part-time" <?php echo (isset($_POST['employee_type']) && $_POST['employee_type'] === 'part-time') ? 'selected' : ''; ?>>Part Time</option>
                    <option value="full-time" <?php echo (isset($_POST['employee_type']) && $_POST['employee_type'] === 'full-time') ? 'selected' : ''; ?>>Full Time</option>
                </select>
            </div>

            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender">
                    <option value="">-- Select Gender --</option>
                    <option value="male" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                    <option value="female" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                </select>
            </div>

            <button type="submit">Create Employee</button>
        </form>
    <?php endif; ?>
</body>
</html>