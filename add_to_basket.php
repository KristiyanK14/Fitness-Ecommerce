<?php
session_start();
include_once "database.php"; 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_basket'])) {
    $product_id = $_POST['productid'];
    if (isset($_SESSION['customerID'])) {
        $customer_id = $_SESSION['customerID'];
        $checkQuery = "SELECT * FROM basket WHERE customerID = '$customer_id' AND productid = '$product_id'";
        $checkResult = mysqli_query($con, $checkQuery);

        if (mysqli_num_rows($checkResult) > 0) {
            $updateQuery = "UPDATE basket SET quantity = quantity + 1 WHERE customerID = '$customer_id' AND productid = '$product_id'";
            mysqli_query($con, $updateQuery);
            echo "<script>alert('Product quantity updated in the basket.');</script>";
        } else {
            $insertQuery = "INSERT INTO basket (customerID, productid, quantity) VALUES ('$customer_id', '$product_id', 1)";
            mysqli_query($con, $insertQuery);
            echo "<script>alert('Product added to the basket.');</script>";
        }

        echo "<script>window.location.href = document.referrer;</script>";
    } else {
        echo "<script>alert('User not logged in');</script>";
    }
}
?>
