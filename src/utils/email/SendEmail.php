<?php

namespace utils\email;

use Exception;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class SendEmail{

    public static function sendMail(string $to, string $subject, string $htmlMessage, string $plainText = "", string $name= ''){
        try{
            $mail = self::emailConnection();
            $mail->addAddress($to, $name);

            $mail->Subject = $subject;
            $mail->Body = $htmlMessage;
            $mail->AltBody = $plainText;

            $ret = $mail->send();
            return $ret;
        }catch(Exception $e){
            return ["error" => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"];
        }
    }

    private static function emailConnection(){
        $mail = new PHPMailer(true);
        try{
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = MAIL_HOST;                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                               //Enable SMTP authentication
            $mail->Username   = MAIL_USERNAME;                     //SMTP username
            $mail->Password   = MAIL_PASSWORD;                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = MAIL_PORT;
            
            $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);

            $mail->isHTML(true);
            $mail->setLanguage("pt_br");
            $mail->CharSet = "UTF-8";

            return $mail;
        }catch(Exception $e){
            throw $e;
            // return ["error" =>  "Mailer Error: {$mail->ErrorInfo}"];
        }
    }
}