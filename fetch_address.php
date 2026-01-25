<?php
include_once "database.php";
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Retrieve the user's address from the customer table
    $sql_fetch_address = "SELECT address, postcode FROM customer WHERE customerID = ?";
    $stmt_fetch_address = mysqli_prepare($con, $sql_fetch_address);
    mysqli_stmt_bind_param($stmt_fetch_address, "i", $user_id);
    mysqli_stmt_execute($stmt_fetch_address);
    $result_fetch_address = mysqli_stmt_get_result($stmt_fetch_address);

    if ($row = mysqli_fetch_assoc($result_fetch_address)) {
        // Return the address details in JSON format
        echo json_encode(array('postcode' => $row['postcode'], 'address' => $row['address']));
    } else {
        echo json_encode(array('postcode' => '', 'address' => ''));
    }
} else {
    echo json_encode(array('postcode' => '', 'address' => ''));
}
?>
