<?php

namespace App\Utils\email;

use App\Utils\HtmlTemplateParse;


class EmailVerification extends SendEmail{

    
    public static function sendEmailVerificationNotification(string $to, string $link, string $tokenExp, string $userName)
    {
        $template = __DIR__ . '/template/EmailVerification.html';
        $open = fopen($template, 'r') or die("error to open file " . $template);
        $read = fread($open, filesize($template));

        $fields = [
            "LINK" => $link,
            "FULLNAME" => $userName,
            "EXPIRATION" => $tokenExp
        ];

        $text = HtmlTemplateParse::parse($read, $fields);

        $plainText = "";

        
        return parent::sendMail($to, "Verificação de e-mail", $text, $plainText);
    }
}