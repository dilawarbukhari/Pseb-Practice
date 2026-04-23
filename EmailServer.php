

<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
class EmailServer
{

public function sendEmail($email, $password)
{
    $mail = new PHPMailer(true);

    try {

       $mail->isSMTP();
            $mail->Host = 'localhost';  
            $mail->SMTPAuth = false;    
            $mail->Port = 2525;      

            $mail->setFrom('admin@example.com', 'Admin');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Account Created';
            $mail->Body = "
    <h3>Your account has been created</h3>
    <p><b>Email:</b> $email</p>
    <p><b>Password:</b> $password</p>
    <p>Please login and go to your profile to change your password.</p>
    <br>
    <p>Regards,<br>
    <b>Technical Solutions</b><br>
    Software Support Team</p>
";
            $mail->send();
            return true;

    } catch (Exception $e) {
        return false;
    }
}
}
?>