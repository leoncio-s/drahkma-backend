<?php

namespace App\Utils\Email;

use Exception;

use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\SMTP;

class SendEmail{

    public static function sendMail(string $to, string $subject, string $htmlMessage, string $plainText = "", string $name= '')
    {
        $mail = self::emailConnection();
        try{
            $mail->addAddress($to, $name);

            $mail->Subject = $subject;
            $mail->Body = $htmlMessage;
            $mail->AltBody = $plainText;

            $ret = $mail->send();
            return $ret;
        }catch(Exception $e){
            // return ["error" => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"];
            throw new Exception("Message could not be sent. Mailer Error: {$e->getMessage()}--{$mail->ErrorInfo}");
        }
    }

    private static function emailConnection(){
        $mail = new PHPMailer(true);
        try{
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = $_ENV["MAIL_HOST"];                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                               //Enable SMTP authentication
            $mail->Username   = $_ENV["MAIL_USERNAME"];                     //SMTP username
            $mail->Password   = $_ENV["MAIL_PASSWORD"];                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = $_ENV["MAIL_PORT"];
            
            $mail->setFrom($_ENV["MAIL_FROM_ADDRESS"], $_ENV["MAIL_FROM_NAME"]);

            $mail->isHTML(true);
            $mail->setLanguage("pt_br");
            $mail->CharSet = "UTF-8";

            return $mail;
        }catch(Exception $e){
            throw new Exception("Mailer Error: {$mail->ErrorInfo}", previous:$e);
        }
    }
}