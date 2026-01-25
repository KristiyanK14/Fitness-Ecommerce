<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
$errors = [];
$successMessage = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["send"])) {
        if (isset($_POST["email"]) && !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Valid email is required";
        }
        if (empty($_POST["c_query"])) {
            $errors[] = "Query cannot be empty";
        }
        if (empty($errors)) {
            $name = $_POST["name"];
            $email = $_POST["email"];
            $c_query = $_POST["c_query"];
            include_once "database.php"; 
            $sql = "INSERT INTO queries (name, email, c_query)
                    VALUES ('$name', '$email', '$c_query')";
            if (mysqli_query($con, $sql)) {
                try {
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
                    $mail->Subject = "Spotter Customer Support Case";
                    $mail->Body = "Dear $name,
                        Thank you for reaching out to Spotter Support! We appreciate your inquiry and the opportunity to assist you.
                        We've received your query: $c_query. Please rest assured that our dedicated team is actively working on addressing your concerns and will provide you with a prompt and comprehensive response.
                        While we work on resolving your query, please feel free to explore our website for more information on our fitness products and services. Should you have any further questions or require assistance, don't hesitate to reach out to us again.
                        Thank you for choosing Spotter. We value your trust and are committed to ensuring your satisfaction with our e-commerce platform.
                        
                        Warm Regards,
                        The Spotter Support Team";

                    $mail->send();

                    $successMessage = "Query submitted successfully and email sent.";
                } catch (Exception $e) {
                    $errors[] = "Email could not be sent. Error: {$mail->ErrorInfo}";
                }
            } else {
                $errors[] = "Error: " . $sql . "<br>" . mysqli_error($con);
            }
        }
    }
}
if (!empty($successMessage)) {
    echo "<script>alert('Query sent successfully, You will recieve an email shortly.');</script>";
    header("Location: contact.php?successMessage=" . urlencode($successMessage));
    exit();
}
if (!empty($errors)) {
    $errorMessages = implode("<br>", $errors);
    header("Location: contact.php?errorMessage=" . urlencode($errorMessages));
    exit();
}
header("Location: contact.php");
exit();
?>
