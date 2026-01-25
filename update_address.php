<?php
include_once "database.php";
session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $postcode = $_POST['postcode'];
    $address = $_POST['address'];
    $sql_update_address = "UPDATE customer SET postcode = ?, address = ? WHERE customerID = ?";
    $stmt_update_address = mysqli_prepare($con, $sql_update_address);
    mysqli_stmt_bind_param($stmt_update_address, "ssi", $postcode, $address, $user_id);
    if (mysqli_stmt_execute($stmt_update_address)) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "session_error";
}
?>
