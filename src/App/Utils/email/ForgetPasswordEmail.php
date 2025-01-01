<?php

namespace App\Utils\email;

use App\Utils\Email\SendEmail;
use App\Utils\HtmlTemplateParse;

class ForgetPasswordEmail extends SendEmail{

    public static function sendEmailForgetPassword(string $email, ?string $name, string $code, ?string $expirationTime = "15")
    {
        $templateName = __DIR__ . '/template/ForgetPassword.html';
        $open = fopen($templateName, 'r') or die("error to open file " . $templateName);
        $toParse = fread($open, filesize($templateName));

        $fields = [
            "CODE" => $code,
            "EXPIRATION" => $expirationTime
        ];

        $parse = HtmlTemplateParse::parse($toParse, $fields);

        $plainText = "Olá!
\n
Você está recebendo este e-mail com o código para redefinição da sua senha na plataforma.
\n            
\n
\n
O código é:
\n
\n
{$code}
\n
\n
O código é valido por {EXPIRATION} minutos.
\n
\n
\n
Este é um e-mail automatico, não responda. Caso não tenha solicitado, pode ignorar este e-mail ou acessar nossa plataforma para alterar a senha.
\n
\n
&copy; 2025 Drahkma. Todos os direitos reservados.
        ";
        return parent::sendMail($email, "Redefinição de Senha", $parse, $plainText, $name);
    }
}