<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Row - Admin Dashboard</title>
    <style>
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

<?php
session_start();
include "navbar.php";
// Check if logged in and if the client is an admin or not
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff')) {
    header("Location: login.php");
    exit();
}

// Include the database connection file
include_once "database.php";

// Retrieve table name from URL parameter
$table = $_GET['table'] ?? '';

// Check if table name is empty
if (empty($table)) {
    echo "Table name is empty.";
    exit();
}

// Function to fetch column names of the table from the database
function getColumnNames($con, $table) {
    $result = mysqli_query($con, "SHOW COLUMNS FROM $table");
    $columns = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $columns[] = $row['Field'];
    }
    return $columns;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $columns = getColumnNames($con, $table);

    // Remove the first column (likely primary key) from the array
    array_shift($columns);

    // Example: Insert data into the database
    $insertValues = array();
    foreach ($columns as $column) {
        $insertValues[] = "'" . mysqli_real_escape_string($con, $_POST[$column]) . "'";
    }
    $insertQuery = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $insertValues) . ")";
    $insertResult = mysqli_query($con, $insertQuery);
    if ($insertResult) {
        echo "Row added successfully.";
    } else {
        echo "Error adding row: " . mysqli_error($con);
    }
}
?>

<div class="container">
    <h2>Add Row - <?php echo $table; ?></h2>
    <form method="post" action="add_row.php?table=<?php echo urlencode($table); ?>">
        <?php
        // Dynamically generate input fields for each column except the first one
        $columns = getColumnNames($con, $table);
        array_shift($columns); // Remove the first column
        foreach ($columns as $column) {
            echo "<label for='$column'>$column:</label>";
            echo "<input type='text' id='$column' name='$column' required><br>";
        }
        ?>
        <button type="submit" name="add">Add</button>
    </form>
</div>

</body>
</html>
