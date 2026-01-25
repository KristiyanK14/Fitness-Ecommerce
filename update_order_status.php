<?php
session_start();
include_once "database.php";

if (isset($_POST['order_id'], $_POST['status'])) {
    $orderId = $_POST['order_id'];
    $status = $_POST['status'];
    $updateQuery = "UPDATE tblorder SET Status = '$status' WHERE OrderID = $orderId";
    if (!mysqli_query($con, $updateQuery)) {
        echo "Error updating order status: " . mysqli_error($con);
        exit();
    }
    header("Location: admin_dashboard.php");
    exit();
} else {
    header("Location: error.php");
    exit();
}
?>

