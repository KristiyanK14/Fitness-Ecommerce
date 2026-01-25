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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $new_name = $_POST['name'];
    $new_email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $update_sql = "UPDATE customer SET name='$new_name', email='$new_email'";
    if (!empty($new_password)) {
        $update_sql .= ", password='$new_password'";
    }
    $update_sql .= " WHERE customerID=$user_id";
    
    if (mysqli_query($con, $update_sql)) {
        header("Location: account.php");
        exit;
    } else {
        echo "Error updating record: " . mysqli_error($con);
    }
}
?>
