<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if(isset($_POST["send"])){
    $mail=new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host='smtp.gmail.com';
    $mail->SMTPAuth=true;
    $mail->Username='spotterstaffteam@gmail.com';
    $mail->Password='ghnlwqgpdbiqaxnw';
    $mail->SMTPSecure='ssl';
    $mail->Port=465;

    $mail->setFrom=('spotterstaffteam@gmail.com');
    $mail->addAddress($_POST["email"]);
    $mail->isHTML(true);
    $mail->Subject="Welcome To The Crew ";
    $mail->Body= "We're thrilled you've joined the Spotter newsletter! Expect fascinating insights and exclusive updates to fuel your spotting passion. Stay tuned!";

    $mail->send();

    echo
    "
    <script>
    alert('Sent Successfully');
    document.location.href='contact.php'
    </script>
    ";



}


?>
