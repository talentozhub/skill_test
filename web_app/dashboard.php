<?php
session_start();
?>
<h1>Dashboard!</h1>

<?php
if (isset($_SESSION['logged_in_user_id']) && $_SESSION['logged_in_user_id']) {
    echo "Employee ID: " . htmlspecialchars($_SESSION['logged_in_user_id']) . " is logged in!";
} else {
    echo "No employee logged in.";
}
?>
