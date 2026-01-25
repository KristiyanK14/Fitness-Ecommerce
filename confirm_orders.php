<?php
session_start();
include_once "navbar.php";
include_once "database.php";
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff')) {
    header("Location: login.php");
    exit();
}
if (isset($_GET['confirm_orders'])) {
    $ordersQuery = "SELECT * FROM tblorder";
    $ordersResult = mysqli_query($con, $ordersQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Orders</title>
</head>
<body>
<h1>Confirm Orders</h1>
<table border="1">
    <tr>
        <th>OrderID</th>
        <th>CustomerID</th>
        <th>Status</th>
        <th>Address</th>
        <th>TotalPrice</th>
        <th>PhoneNumber</th>
        <th>Postcode</th>
    </tr>
    <?php
    while ($row = mysqli_fetch_assoc($ordersResult)) {
        ?>
        <tr>
            <td><?php echo $row['OrderID']; ?></td>
            <td><?php echo $row['CustomerID']; ?></td>
            <td>
                <form action="update_order_status.php" method="post">
                    <input type="hidden" name="order_id" value="<?php echo $row['OrderID']; ?>">
                    <select name="status">
                        <option value="pending" <?php if ($row['Status'] == 'pending') echo 'selected'; ?>>Pending</option>
                        <option value="delivering" <?php if ($row['Status'] == 'delivering') echo 'selected'; ?>>Delivering</option>
                        <option value="delivered" <?php if ($row['Status'] == 'delivered') echo 'selected'; ?>>Delivered</option>
                    </select>
                    <input type="submit" value="Update">
                </form>
            </td>
            <td><?php echo $row['Address']; ?></td>
            <td><?php echo $row['TotalPrice']; ?></td>
            <td><?php echo $row['PhoneNumber']; ?></td>
            <td><?php echo $row['Postcode']; ?></td>
        </tr>
        <?php
    }
    ?>
</table>
</body>
</html>
<?php
}
?>
