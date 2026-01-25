<?php
include_once "database.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if(isset($_POST["reply"]) && isset($_POST["queryId"])) {
    $queryId = $_POST["queryId"];
    $email = $_POST["email"];
    $reply = $_POST["reply"];
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'spotterstaffteam@gmail.com';
    $mail->Password = 'ghnlwqgpdbiqaxnw';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('spotterstaffteam@gmail.com');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = "Query Reply. ID: $queryId";
    $mail->Body = $reply;

    try {
        $mail->send();
        $updateQuery = "UPDATE queries SET replied = 1 WHERE queryid = $queryId";
        if (!mysqli_query($con, $updateQuery)) {
            throw new Exception("Failed to update replied column: " . mysqli_error($con));
        }

        echo "<script>alert('Reply sent successfully.');</script>";
    } catch (Exception $e) {
        error_log('Error sending email: ' . $e->getMessage(), 0);
        echo "<script>alert('Failed to send reply.');</script>";
    }
}

?>

