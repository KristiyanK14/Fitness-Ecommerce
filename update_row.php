<?php
session_start();
include_once "database.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$table = $_GET['table'] ?? '';
$rowData = $_POST['data'] ?? '';
if (empty($table) || empty($rowData)) {
    header("Location: manage_tables.php");
    exit();
}
$result = mysqli_query($con, "SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'");
$row = mysqli_fetch_assoc($result);
$primaryKey = $row['Column_name'] ?? '';
if (empty($primaryKey) || !isset($rowData[$primaryKey])) {
    header("Location: manage_tables.php");
    exit();
}
$updateQuery = "UPDATE $table SET ";
foreach ($rowData as $column => $value) {
    if ($column !== $primaryKey) {
        $updateQuery .= "$column = '" . mysqli_real_escape_string($con, $value) . "', ";
    }
}
$updateQuery = rtrim($updateQuery, ', ');
$updateQuery .= " WHERE $primaryKey = '" . mysqli_real_escape_string($con, $rowData[$primaryKey]) . "'";
$result = mysqli_query($con, $updateQuery);
if ($result) {
    echo("Edit is successful ");
    exit;
} else {
    echo "Error updating row: " . mysqli_error($con);
    exit;
}
?>
