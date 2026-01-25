<?php
include_once "process-login.php";
if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('This page can only be accessed when logged in, Please log in.');
            setTimeout(function() {
                window.location.href = 'login.php';
            }, 100);
          </script>";
    exit;
}
$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM customer WHERE customerID = $user_id";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);
if ($row) {
    $user_name = $row['name'];
    $user_email = $row['email'];
    $user_address = $row['address']; 
    $user_postcode = $row['postcode']; 
    $user_password = $row['Hpassword']; 
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $new_name = $_POST['name'];
    $new_email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $new_address = $_POST['address']; 
    $new_postcode = $_POST['postcode']; 
    $update_sql = "UPDATE customer SET name='$new_name', email='$new_email', address='$new_address', postcode='$new_postcode'";
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_sql .= ", Hpassword='$hashed_password'"; 
    }
    $update_sql .= " WHERE customerID=$user_id";

    if (mysqli_query($con, $update_sql)) {

        header("Location: account.php");
        exit;
    } else {

        echo "Error updating record: " . mysqli_error($con);
    }
}
$order_sql = "SELECT * FROM tblorder WHERE CustomerID = $user_id";
$order_result = mysqli_query($con, $order_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .details-box {
            text-align: center;
            border: 5px solid #ccc;
            padding: 20px;
            margin: auto;
            max-width: 400px;
            float: left;
            margin-right: 20px;
        }
        .details-box h2 {
            margin-top: 0;
        }
        .details-box p {
            margin: 10px 0;
        }
        .edit-button {
            background-color: #4CAF50; /
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-top: 10px;
            cursor: pointer;
        }
        .edit-button:hover {
            background-color: #45a049;
        }
        .edit-form {
            text-align: center;
            border: 5px solid #ccc;
            padding: 20px;
            margin: auto;
            max-width: 400px;
            display: none;
        }
        .edit-form label {
            display: block;
        }
        .edit-form input[type="text"],
        .edit-form input[type="email"],
        .edit-form input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }
        .edit-form input[type="submit"] {
            background-color: #4CAF50; 
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-top: 10px;
            cursor: pointer;
        }
        .edit-form input[type="submit"]:hover {
            background-color: #45a049;
        }
        .orders-box {
            float: left;
            border: 5px solid #ccc;
            padding: 20px;
            margin-top: 20px;
            max-width: 400px;
        }
        .orders-box h2 {
            margin-top: 0;
        }
        .order-table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-table th,
        .order-table td {
            padding: 8px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
<?php $currentPage = 'account';
include "navbar.php"; ?>
    <h1 style="text-align: center;">Welcome, <?php echo $user_name; ?></h1>
    <div class="details-box">
    <h2>Account Information</h2>
    <p>Name: <?php echo $user_name; ?></p>
    <p>Email: <?php echo $user_email; ?></p>
    <p>Address: <?php echo $user_address; ?></p>
    <p>Postcode: <?php echo $user_postcode; ?></p>
    <p>Password: ********</p>
    <button class="edit-button" id="editButton">Edit Information</button>
</div>
<div class="edit-form" id="editForm">
    <h2>Edit Account Information</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $user_name; ?>">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $user_email; ?>">
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" value="">
        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?php echo $user_address; ?>">
        <label for="postcode">Postcode:</label>
        <input type="text" id="postcode" name="postcode" value="<?php echo $user_postcode; ?>">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <input type="submit" name="update" value="Update">
    </form>
</div>
<div class="orders-box">
    <h2>Orders</h2>
    <table class="order-table">
        <thead>
            <tr>
                <th>OrderID</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Action</th> 
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($order_result) > 0) {
                while ($order_row = mysqli_fetch_assoc($order_result)) {
                    echo "<tr>";
                    echo "<td>{$order_row['OrderID']}</td>";
                    echo "<td> £{$order_row['TotalPrice']}</td>";
                    echo "<td>{$order_row['Status']}</td>";
                    echo "<td><a href='order_details.php?order_id={$order_row['OrderID']}'>View Details</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No orders found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
<script>
    document.getElementById('editButton').addEventListener('click', function() {
        document.getElementById('editForm').style.display = 'block';
        document.querySelector('.details-box').style.display = 'none';
    });
</script>
</body>
</html>
