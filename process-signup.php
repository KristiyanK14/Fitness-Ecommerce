<?php
include_once "database.php";

$errors = array();

if (empty($_POST["name"])) {
    $errors[] = "Name Is Required";
}
if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Valid email is required";
}
$password = trim($_POST['password']);
if(strlen($password) < 8){
    $errors[] = "Password must be longer than 8 characters";
}
if (!preg_match("/[a-z]/i", $_POST["password"])) {
    $errors[] = "Password must contain at least one letter";
}
if (!preg_match("/[0-9]/i", $_POST["password"])) {
    $errors[] = "Password must contain at least one digit";
}
if ($_POST["password"] !== $_POST["password_confirmation"]) {
    $errors[] = "Passwords must match";
}

$email = mysqli_real_escape_string($con, $_POST["email"]);
$sql_check_email = "SELECT * FROM customer WHERE email = '$email'";
$result = mysqli_query($con, $sql_check_email);
if (mysqli_num_rows($result) > 0) {
    $errors[] = "An account already exists with this email";
}

if (!empty($errors)) {
 
    header("Location: signup.php?errors=" . urlencode(implode("\n", $errors)));
    exit(); 
}


$name = $_POST["name"];
$email = $_POST["email"];
$password = $_POST["password"];

$hashedPassword = password_hash($password, PASSWORD_DEFAULT); 

$sql = "INSERT INTO customer (customerID, name, email, Hpassword, verification)
        VALUES(0, '$name', '$email', '$hashedPassword', NULL)";

if (mysqli_query($con, $sql)) {
    echo "New record created successfully";
    header("Location: login.php");
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($con);
}
?>
