<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
require_once 'PHPMailer/src/Exception.php';

function sendOTPEmail($to, $code) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com'; // replace with actual
        $mail->SMTPAuth = true;
        $mail->Username = 'no-reply@jobvisaworld.com'; // your Hostinger email
        $mail->Password = 'Lexep@786###'; // your email password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('no-reply@jobvisaworld.com', 'JobVisaWorld');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = 'Verify your JobVisaWorld Account';
        $mail->Body = "Hi, <br><br> Your OTP code is: <b>$code</b><br><br>Enter it to verify your account.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}