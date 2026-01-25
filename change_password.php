<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // retrieving the stored email
    $storedEmail = $_SESSION['reset_email'];

    // validating the pass
    $newPassword = mysqli_real_escape_string($con, $_POST["new_password"]);

    // Update the password in the customer table
    $updatePasswordQuery = "UPDATE customer SET password = '$newPassword' WHERE email = '$storedEmail'";
    if (mysqli_query($con, $updatePasswordQuery)) {
        echo "Password updated successfully";
        // Clear the session variables
        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_code']);
    } else {
        echo "Error updating password";
    }
}
?>

<!-- HTML form on change_password.php -->
<form action="change_password.php" method="post">
    <label for="new_password">Enter your new password:</label>
    <input type="password" id="new_password" name="new_password" required>
    <button type="submit">Change Password</button>
</form>
