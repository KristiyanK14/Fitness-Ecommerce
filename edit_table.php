<?php
session_start();
include_once "database.php";
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff')) {
    header("Location: login.php");
    exit();
}
$table = $_GET['table'] ?? '';
$rowDataEncoded = $_GET['data'] ?? '';
if (empty($table) || empty($rowDataEncoded)) {
    header("Location: manage_tables.php"); 
    exit();
}
$rowData = json_decode(urldecode($rowDataEncoded), true);
if ($rowData === null) {
    header("Location: manage_tables.php"); 
    exit();
}

$result = mysqli_query($con, "SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'");
$row = mysqli_fetch_assoc($result);
$primaryKey = $row['Column_name'] ?? '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Row - Admin Dashboard</title>
    <style>
        /* Your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        input[type="text"] {
            width: calc(100% - 22px);
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button[type="submit"] {
            padding: 10px 20px;
            background-color: grey;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button[type="submit"]:hover {
            background-color: lightgrey;
        }
    </style>
</head>
<body>
<?php include "navbar.php"; ?>

<div class="container">
    <h2>Edit Row - <?php echo $table; ?></h2>
    <form method="post" action="update_row.php?table=<?php echo urlencode($table); ?>&data=<?php echo urlencode($rowDataEncoded); ?>">
        <input type="hidden" name="table" value="<?php echo $table; ?>">
        <?php
        foreach ($rowData as $column => $value) {
            if ($column !== $primaryKey) {
                echo "<label for='$column'>$column:</label>";
                echo "<input type='text' id='$column' name='data[$column]' value='$value' required><br>";
            } else {
                echo "<input type='hidden' name='data[$column]' value='$value'>";
            }
        }
        ?>
        <button type="submit" name="update">Update</button>
    </form>
</div>

</body>
</html>
