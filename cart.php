<?php
include_once "database.php";
session_start();
include "navbar.php";

// Assuming $user_id is set when the user logs in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Retrieve all baskets for the user
    $sql = "SELECT basketid FROM basket WHERE customerid = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);


    // Display products for all baskets in a single table
    echo "<h1 style='text-align: center; background-color: #f0f0f0; padding: 10px;'>Your Basket:</h1>";
    echo "<table border='1' style='width: 90%; margin: 0 auto; border-collapse: collapse;'>
            <tr>
                <th>Product Image</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Action</th>
            </tr>";

    $total_price = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        $basket_id = $row['basketid'];

        // Retrieve products in the user's basket
        $sql_products = "SELECT p.productid, p.pname,p.imageurl, p.price,p.stock, b.quantity FROM products p
                        INNER JOIN basket b ON p.productid = b.productid
                        WHERE b.basketid = ?";
        $stmt_products = mysqli_prepare($con, $sql_products);
        mysqli_stmt_bind_param($stmt_products, "i", $basket_id);
        mysqli_stmt_execute($stmt_products);
        $result_products = mysqli_stmt_get_result($stmt_products);
      
        while ($row_product = mysqli_fetch_assoc($result_products)) {
            echo "<tr>";
            echo "<td><img src='{$row_product['imageurl']}' style='width: 200px; height: auto;' /></td> ";
            echo "<td>{$row_product['pname']}</td>";
            echo "<td>£{$row_product['price']}</td>";
            echo "<td>";
            echo "<button class='quantity-modify' data-type='minus' data-productid='{$row_product['productid']}' data-stock='{$row_product['stock']}'>-</button>";
            echo "<span class='quantity'>{$row_product['quantity']}</span>";
            echo "<button class='quantity-modify' data-type='plus' data-productid='{$row_product['productid']}' data-stock='{$row_product['stock']}'>+</button>";
            echo "</td>";
            echo "<td>";
            echo "<button class='remove-product' data-productid='{$row_product['productid']}'>Remove</button>"; // Add Remove button
            echo "</td>";
            echo "</tr>";

            // Calculate total price
            $total_price += $row_product['price'] * $row_product['quantity'];
        }
    }

    echo "</table>";

    // Display total price for all baskets
    echo "<p style='text-align: center; background-color: #f0f0f0; padding: 10px;'>Total Price: £<span id='total-price'>$total_price</span></p>";

    // Add input box for discount code
    echo "<form method='post' action=''>
            <label for='discount'>Enter Discount Code:</label>
            <input type='text' id='discount' name='discount'>
            <button type='submit' name='apply_discount'>Apply Discount</button>
          </form>";

    // Apply discount if discount code is submitted
    if (isset($_POST['apply_discount'])) {
        $discount_code = $_POST['discount'];

        // Check if the discount code exists and has remaining quantity
        $sql_discount = "SELECT amount, quantity FROM discount WHERE code = ? AND quantity > 0";
        $stmt_discount = mysqli_prepare($con, $sql_discount);
        mysqli_stmt_bind_param($stmt_discount, "s", $discount_code);
        mysqli_stmt_execute($stmt_discount);
        $result_discount = mysqli_stmt_get_result($stmt_discount);

        if ($row_discount = mysqli_fetch_assoc($result_discount)) {
            $discount_amount = $row_discount['amount'];
            $total_price -= $discount_amount;

            // Update the quantity of the discount code
            $new_quantity = $row_discount['quantity'] - 1;
            $sql_update_quantity = "UPDATE discount SET quantity = ? WHERE code = ?";
            $stmt_update_quantity = mysqli_prepare($con, $sql_update_quantity);
            mysqli_stmt_bind_param($stmt_update_quantity, "is", $new_quantity, $discount_code);
            mysqli_stmt_execute($stmt_update_quantity);

            // Display updated total price
            echo "<script>document.getElementById('total-price').innerText = '$total_price';</script>";
            echo "<p style='color: green;'>Discount applied successfully!</p>";
        } else {
            echo "<p style='color: red;'>Invalid discount code or quantity expired!</p>";
        }
    }

} else {
    // Redirect to the login page if the user is not logged in
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal JS SDK Standard Integration</title>
    <style>
        .container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            width: 90%;
            margin: 20px auto;
        }

        .shipping-address {
            width: 45%;
            padding: 10px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            height: auto;
        }

        .shipping-address input {
            display: block;
            margin-bottom: 10px;
            width: calc(100% - 20px);
            padding: 5px;
            font-size: 14px;
        }

        .paypal-container {
            width: 45%;
            text-align: center;
        }

        .paypal-button-container {
            margin-top: 20px;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
    </style>
</head>
<body>

    <?php
            // Retrieve shipping details from the form
        $postcode = isset($_POST['postcode']) ? $_POST['postcode'] : '';
        $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
        $address = isset($_POST['address']) ? $_POST['address'] : '';

        // Set shipping details in session variables
        $_SESSION['postcode'] = $postcode;
        $_SESSION['phone'] = $phone;
        $_SESSION['address'] = $address;

        
    ?>

<div class="container">
    <div class="shipping-address">
        <h2>Shipping Address</h2>
        <form id="address-form" method="post">
            <input type="text" name="postcode" id="postcode" placeholder="Postcode">
            <input type="text" name="address" id="address" placeholder="Address">
            <button type="button" id="confirm-address">Confirm Address</button>
        </form>
        <div id="success-message" style="display: none;">Address updated successfully!</div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#confirm-address').click(function () {
            var postcode = $('#postcode').val();
            var address = $('#address').val();

            if (postcode.trim() === '' || address.trim() === '') {
                alert('Please fill in both postcode and address fields.');
                return;
            }

            $.ajax({
                url: 'cart.php',
                method: 'POST',
                data: { postcode: postcode, address: address },
                success: function (response) {
                    $('#success-message').show();

                },
                error: function () {
                    alert('Error occurred while updating address');
                }
            });
        });
    });
</script>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['postcode']) && isset($_POST['address'])) {
        $postcode = $_POST['postcode'];
        $address = $_POST['address'];
        $sql = "UPDATE customer SET postcode = '$postcode', address = '$address' WHERE customerid = $user_id";
        mysqli_query($con, $sql);

    }
}
?>



    <div class="paypal-container">
        <div id="paypal-button-container"></div>
    </div>
</div>

<p id="result-message"></p>
<!-- Replace the "test" client-id value with your client-id -->
<script src="https://www.paypal.com/sdk/js?AXyCMchUugtD_qTturH7tn9ncqlVB8WyqzYOGt4NggUTZ4F0QhNVGtYA1b5t_IHkHYShTe5V4HLYdHWi=test&components=buttons&enable-funding=venmo&disable-funding=paylater,card" data-sdk-integration-source="integrationbuilder_sc"></script>
<script src="app.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php
// paypal api account seller token identifier
$token = "AYh1gZ8WHFZNQ1-76jQvTilVN424MRsiHtqiQPCqZFCzZ6gOTHNvbcRODenwh4XPpsH4o3h4QI_tI-KB";
?>
<script src="https://www.paypal.com/sdk/js?client-id=<?= $token; ?>&currency=GBP"></script>
<script>
    $(document).ready(function () {
        // PayPal button initialization
        paypal.Buttons({
            onClick: function () {
                // Validate shipping address
                if ($('#postcode').val() === '' || $('#road').val() === '' || $('#phone').val() === '' || $('#house_number').val() === '') {
                    alert('Please fill in all shipping address fields.');
                    return false; // Prevent checkout
                }
            },

            // PayPal button styling
            style: {
                shape: 'pill'
            },

            // Create order function
            createOrder: function (data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: <?= $total_price; ?>
                        }
                    }]
                });
            },


            // On approve function
        onApprove: function (data, actions) {
            return actions.order.capture().then(function (orderData) {
                var total_price = <?= $total_price; ?>;
                // Redirect to confirm_purchase.php with the total price only
                window.location.replace(`http://localhost/COURSE/confirm_purchase.php?total_price=${total_price}`);
                


                    const transaction = orderData.purchase_units[0].payments.captures[0];

                    var data = {
                        'payment_method': "paypal",
                        'PaymentID': transaction.id // This is the PayPal Transaction ID that's fetched
                    
                    };
                    $.ajax({
                        method: "POST",
                        url: "confirm_purchase.php", // Change this to point to your confirm_purchase.php file
                        data: data,
                        success: function (response) {
                            if (response == 201) {
                                window.location.href = "http://localhost/programs/coursework/design-email/order.php";
                            } else {
                                console.log(response); // Log error response
                            }
                        }
                    });
                });
            }
        }).render('#paypal-button-container');

        // Confirm address button click event
        $('#confirm-address').click(function () {
            if ($('#postcode').val() === '' || $('#road').val() === '' || $('#phone').val() === '') {
                alert('Please fill in all shipping address fields.');
                return false; // Prevent checkout
            } else {
                alert('Address confirmed! Proceed to checkout.');
            }
        });

        // Quantity modification button click event
        $('.quantity-modify').click(function () {
    var type = $(this).data('type');
    var productId = $(this).data('productid');
    var stock = $(this).data('stock');
    var quantityElement = $(this).siblings('.quantity');
    var currentQuantity = parseInt(quantityElement.text());

    if (type === 'plus' && currentQuantity >= stock) {
        alert('Cannot add more. Maximum quantity reached.');
        return;
    } else if (type === 'minus' && currentQuantity <= 1) {
        alert('Cannot decrease further.');
        return;
    }

            // Send AJAX request to update the quantity in the database
            $.ajax({
                url: 'update.quantity.php', // Change this to the script that updates the quantity
                method: 'POST',
                data: { product_id: productId, type: type },
                success: function (response) {
                    // If the update is successful, update the displayed quantity
                    if (response === 'success') {
                        if (type === 'minus' && currentQuantity > 1) {
                            quantityElement.text(currentQuantity - 1);
                        } else if (type === 'plus') {
                            quantityElement.text(currentQuantity + 1);
                        }
                        location.reload()
                    } else {
                        alert('Failed to update quantity');
                    }
                },
                error: function () {
                    alert('Error occurred while updating quantity');
                }
            });
        });

        // Remove product button click event
        $('.remove-product').click(function () {
            var productId = $(this).data('productid');
            var rowToRemove = $(this).closest('tr');

            // Send AJAX request to remove the product from the basket
            $.ajax({
                url: 'remove_product.php', // Change this to the script that removes the product
                method: 'POST',
                data: { product_id: productId },
                success: function (response) {
                    // If removal is successful, remove the corresponding row from the table
                    if (response === 'success') {
                        rowToRemove.remove();
                        location.reload()
                    } else {
                        alert('Failed to remove product');
                    }
                },
                error: function () {
                    alert('Error occurred while removing product');
                }
            });
        });
    });

    
</script>

</body>
</html>
