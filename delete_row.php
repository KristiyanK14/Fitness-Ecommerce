<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff')) {
    header("Location: login.php");
    exit();
}
include_once "database.php";
$table = $_POST['table'] ?? '';
$primaryKeyColumnName = $_POST['primaryKeyColumnName'] ?? '';
$primaryKeyValue = $_POST['primaryKeyValue'] ?? '';
if (empty($table) || empty($primaryKeyColumnName) || empty($primaryKeyValue)) {
    echo "Error: Table name, primary key column name, or primary key value is missing.";
    exit();
}
$query = "DELETE FROM $table WHERE $primaryKeyColumnName = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 's', $primaryKeyValue);
$result = mysqli_stmt_execute($stmt);
if ($result) {
    echo "Row deleted successfully from $table.";
} else {
    echo "Error deleting row: " . mysqli_error($con);
}
mysqli_stmt_close($stmt);
mysqli_close($con);
?>
