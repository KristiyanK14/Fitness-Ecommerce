<?php
session_start();
include_once "navbar.php";
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff')) {
    header("Location: login.php");
    exit();
}
include_once "database.php";
$revenueQuery = "SELECT SUM(CAST(TotalPrice AS DECIMAL(10,2))) AS totalRevenue FROM tblorder";
$revenueResult = mysqli_query($con, $revenueQuery);
$totalRevenue = 0;
if ($revenueResult && mysqli_num_rows($revenueResult) > 0) {
    $revenueRow = mysqli_fetch_assoc($revenueResult);
    $totalRevenue = $revenueRow['totalRevenue'];
}

$orderCountQuery = "SELECT COUNT(OrderId) AS orderCount FROM tblorder";
$orderCountResult = mysqli_query($con, $orderCountQuery);
$totalOrders = 0;
if ($orderCountResult && mysqli_num_rows($orderCountResult) > 0) {
    $orderCountRow = mysqli_fetch_assoc($orderCountResult);
    $totalOrders = $orderCountRow['orderCount'];
}
$userCountQuery = "SELECT COUNT(customerID) AS userCount FROM customer";
$userCountResult = mysqli_query($con, $userCountQuery);
$totalUsers = 0;
if ($userCountResult && mysqli_num_rows($userCountResult) > 0) {
    $userCountRow = mysqli_fetch_assoc($userCountResult);
    $totalUsers = $userCountRow['userCount'];
}
$queryCountQuery = "SELECT COUNT(queryid) AS queryCount FROM queries WHERE replied != 1";
$queryCountResult = mysqli_query($con, $queryCountQuery);
$totalQueries = 0;
if ($queryCountResult && mysqli_num_rows($queryCountResult) > 0) {
    $queryCountRow = mysqli_fetch_assoc($queryCountResult);
    $totalQueries = $queryCountRow['queryCount'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        h2 {
            font-size: 50px;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: flex-start; 
            height: 100vh;
            flex-wrap: wrap;
        }

        .rectangle {
            width: 300px;
            height: 150px;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            margin: 20px;
            padding: 20px;
            text-align: center;
            line-height: 1.5em;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="rectangle">
        <h2>Revenue</h2>
        <p>Total Revenue is £<?php echo $totalRevenue; ?></p>
    </div>

    <div class="rectangle">
        <h2>Orders</h2>
        <p><?php echo $totalOrders; ?> orders have been made.</p>
        <form action="confirm_orders.php" method="get">
            <button type="submit" name="confirm_orders">Confirm Orders</button>
        </form>
    </div>

    <div class="rectangle">
        <h2>Users</h2>
        <p>There are <?php echo $totalUsers; ?> users.</p>
    </div>

    <div class="rectangle">
    <h2>Queries</h2>
    <p>Total Queries: <?php echo $totalQueries; ?></p>
    <form action="reply_to_queries.php" method="get">
        <button type="submit" name="reply_to_queries">Reply to Queries</button>
    </form>
</div>
</div>
</body>
</html>
<?php
mysqli_close($con);
?>
