<?php
session_start();
include_once "database.php";
if (!empty($_POST["name"]) && !empty($_POST["email"]) && !empty($_POST["password"])) {
    $name = mysqli_real_escape_string($con, $_POST["name"]);
    $email = mysqli_real_escape_string($con, $_POST["email"]);
    $password = $_POST["password"];
    $sql = "SELECT * FROM customer WHERE name = '$name' AND email = '$email'";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        if (password_verify($password, $row['Hpassword'])) {
            echo "<h1><center>Login successful</center></h1>";
            $_SESSION['user_id'] = $row['customerID'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_email'] = $row['email'];
            $_SESSION['user_role'] = $row['role'];
            if ($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'staff') {
                header("Location: admin_dashboard.php");
            } else {
                $_SESSION['customerID'] = $row['customerID']; 
                header("Location: account.php");
            }
            exit;
        } else {
            echo "<h1>Login failed. Invalid password.</h1>";
        }
    } else {
        echo "<h1>Login failed. User not found.</h1>";
    }
}
?>
