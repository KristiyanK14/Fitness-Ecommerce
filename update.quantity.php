<?php
include_once "database.php";

if(isset($_POST['product_id']) && isset($_POST['type'])) {
    $product_id = $_POST['product_id'];
    $type = $_POST['type'];
    $sql_select = "SELECT quantity FROM basket WHERE productid = ?";
    $stmt_select = mysqli_prepare($con, $sql_select);
    mysqli_stmt_bind_param($stmt_select, "i", $product_id);
    mysqli_stmt_execute($stmt_select);
    $result_select = mysqli_stmt_get_result($stmt_select);
    $row = mysqli_fetch_assoc($result_select);
    $current_quantity = $row['quantity'];
    if($type === 'plus') {
        $new_quantity = $current_quantity + 1;
    } elseif($type === 'minus' && $current_quantity > 1) {
        $new_quantity = $current_quantity - 1;
    } else {
        echo "error";
        exit();
    }
    $sql_update = "UPDATE basket SET quantity = ? WHERE productid = ?";
    $stmt_update = mysqli_prepare($con, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "ii", $new_quantity, $product_id);
    if(mysqli_stmt_execute($stmt_update)) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "error";
}
?>
