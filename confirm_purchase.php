<?php
session_start();
include_once "database.php";
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
include_once "navbar.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['total_price'])) {
    $total_price = $_GET['total_price'];
} else {
    $total_price = 0; 
}


$sql_fetch_shipping_details = "SELECT address, postcode FROM customer WHERE customerid = $user_id";
$result_fetch_shipping_details = mysqli_query($con, $sql_fetch_shipping_details);

if ($row_shipping_details = mysqli_fetch_assoc($result_fetch_shipping_details)) {
    $address = $row_shipping_details['address'];
    $postcode = $row_shipping_details['postcode'];
}
$email_body = "<h2>Invoice for Your Purchase</h2>";
$email_body .= "<p>Dear {$_SESSION['user_name']},</p>";
$email_body .= "<p>Thank you for your recent purchase. Below is the invoice detailing your order:</p>";

$email_body .= "<table border='1'>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>";

$sql_fetch_products = "SELECT p.productid, p.pname, p.price, b.quantity FROM products p INNER JOIN basket b ON p.productid = b.productid WHERE b.customerid = ? AND b.checkout_status = 0";
$stmt_fetch_products = mysqli_prepare($con, $sql_fetch_products);
mysqli_stmt_bind_param($stmt_fetch_products, "i", $user_id);
mysqli_stmt_execute($stmt_fetch_products);
$result_fetch_products = mysqli_stmt_get_result($stmt_fetch_products);

$order_items = []; 

while ($row = mysqli_fetch_assoc($result_fetch_products)) {
    $product_id = $row['productid'];
    $quantity = $row['quantity'];
    $product_price = $row['price'];


    $order_items[] = [
        'product_id' => $product_id,
        'pname' => $row['pname'],
        'quantity' => $quantity,
        'price' => $product_price
    ];

    $email_body .= "<tr>
                        <td>{$product_id}</td>
                        <td>{$row['pname']}</td>
                        <td>{$quantity}</td>
                        <td>£{$product_price}</td>
                    </tr>";
}

$email_body .= "</table>";

$email_body .= "<p><strong>Total Price:</strong> £{$total_price}</p>";
$email_body .= "<p>Thank you for shopping with us!</p>";

$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'spotterstaffteam@gmail.com';
$mail->Password = 'ghnlwqgpdbiqaxnw';
$mail->SMTPSecure = 'ssl';
$mail->Port = 465;

$mail->setFrom('spotterstaffteam@gmail.com');
$mail->addAddress($_SESSION['user_email']);
$mail->isHTML(true);
$mail->Subject = "Invoice for Your Purchase";
$mail->Body = $email_body;


$status = "Pending"; 


if ($total_price > 0) {
    $mail->send();
    $sql_order = "INSERT INTO tblorder (customerID, Status, Address, TotalPrice, Postcode) VALUES (?, ?, ?, ?, ?)";
$stmt_order = mysqli_prepare($con, $sql_order);
mysqli_stmt_bind_param($stmt_order, "issss", $user_id, $status, $address, $total_price, $postcode);
mysqli_stmt_execute($stmt_order);



    $order_id = mysqli_insert_id($con);

    foreach ($order_items as $item) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];

        $sql_orderline = "INSERT INTO orderlineid (ProductID, OrderID, Quantity) VALUES (?, ?, ?)";
        $stmt_orderline = mysqli_prepare($con, $sql_orderline);
        mysqli_stmt_bind_param($stmt_orderline, "iii", $product_id, $order_id, $quantity);
        mysqli_stmt_execute($stmt_orderline);

        $sql_update_stock = "UPDATE products SET stock = stock - ? WHERE productid = ?";
        $stmt_update_stock = mysqli_prepare($con, $sql_update_stock);
        mysqli_stmt_bind_param($stmt_update_stock, "ii", $quantity, $product_id);
        mysqli_stmt_execute($stmt_update_stock);

        mysqli_stmt_close($stmt_orderline);
        mysqli_stmt_close($stmt_update_stock);
    }

    $sql_delete_basket_products = "DELETE FROM basket WHERE customerid = ? AND checkout_status = 0";
    $stmt_delete_basket_products = mysqli_prepare($con, $sql_delete_basket_products);
    mysqli_stmt_bind_param($stmt_delete_basket_products, "i", $user_id);
    mysqli_stmt_execute($stmt_delete_basket_products);

    mysqli_stmt_close($stmt_delete_basket_products);

    mysqli_close($con);

    echo "<!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Invoice</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                    }
                    .invoice {
                        margin: 50px auto;
                        width: 50%;
                        border: 2px solid #000;
                        padding: 20px;
                        text-align: center;
                    }
                </style>
            </head>
            <body>
                <div class='invoice'>
                    <p>Thank you for your purchase. An invoice has been sent to your email.</p>
                </div>
            </body>
            </html>";
} else {
    echo "<!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Invoice</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                    }
                    .invoice {
                        margin: 50px auto;
                        width: 50%;
                        border: 2px solid #000;
                        padding: 20px;
                        text-align: center;
                    }
                </style>
            </head>
            <body>
                <div class='invoice'>
                    <p>Thank you for your purchase. An invoice has been sent to your email.</p>
                </div>
            </body>
            </html>";
}
?>
