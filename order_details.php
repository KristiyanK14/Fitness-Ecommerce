<?php session_start();
include_once "navbar.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <style>
        .order-details {
            width: 50%;
            margin: 50px auto;
            border: 2px solid #ccc;
            padding: 20px;
        }
        .order-details h2 {
            text-align: center;
        }
        .order-items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-items-table th,
        .order-items-table td {
            padding: 8px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
<div class="order-details">
    <?php
    include_once "database.php";

    if(isset($_GET['order_id'])) {
        $order_id = $_GET['order_id'];

        $sql = "SELECT * FROM tblorder WHERE OrderID = $order_id";
        $result = mysqli_query($con, $sql);

        if(mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $total_price = $row['TotalPrice'];
            $status = $row['Status'];

            echo "<h2>Order Details</h2>";
            echo "<p><strong>Order ID:</strong> $order_id</p>";
            echo "<p><strong>Total Price:</strong> $total_price</p>";
            echo "<p><strong>Status:</strong> $status</p>";

            // Display order items
            $sql_items = "SELECT p.imageurl, p.pname, p.price, ol.quantity AS Quantity
              FROM tblorder o
              INNER JOIN orderlineid ol ON o.OrderID = ol.OrderID
              INNER JOIN products p ON ol.ProductID = p.ProductID
              WHERE o.OrderID = $order_id";
            $result_items = mysqli_query($con, $sql_items);

            if(mysqli_num_rows($result_items) > 0) {
                echo "<h3>Order Items</h3>";
                echo "<table class='order-items-table'>";
                echo "<thead><tr><th>Product Image</th><th>Product Name</th><th>Price</th><th>Quantity</th></tr></thead>";
                echo "<tbody>";

                while($item_row = mysqli_fetch_assoc($result_items)) {
                    $product_image = $item_row['imageurl'];
                    $product_name = $item_row['pname'];
                    $product_price = $item_row['price'];
                    $product_quantity = $item_row['Quantity'];

                    echo "<tr>";
                    echo "<td><img src='$product_image' style='max-width: 100px; max-height: 100px;' /></td>";
                    echo "<td>$product_name</td>";
                    echo "<td>£$product_price</td>";
                    echo "<td>$product_quantity</td>";
                    echo "</tr>";
                }

                echo "</tbody></table>";
            } else {
                echo "<p>No order items found.</p>";
            }
        } else {
            echo "<p>No order found with ID: $order_id</p>";
        }
    } else {
        echo "<p>No order ID specified.</p>";
    }
    ?>
</div>
</body>
</html>
