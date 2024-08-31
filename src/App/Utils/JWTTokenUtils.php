<?php

namespace App\Utils;

use App\Users\User;
use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;

class JWTTokenUtils{

    public static function generate(User $userData){
        $payload = [
            "alg" => "HS256",
            "typ" => "JWT"
        ];
        $body = [
            'iat' => 0,
            'exp' => 0,
            'iss' => $userData->getEmail(),
            'data' => $userData->toJson()
        ];

        $now = new DateTime('now', new DateTimeZone('america/sao_paulo'));
        $exp = new DateTime('now', new DateTimeZone('america/sao_paulo'));
        $exp->add(DateInterval::createFromDateString('1 day'));

        $body['iat'] = $now->getTimestamp();
        $body['exp'] = $exp->getTimestamp();

        try{
            $payload_enc = Utils::base64url_encode(json_encode($payload));
            $body_enc = Utils::base64url_encode(json_encode($body));

            $toSign = $payload_enc . '.' . $body_enc;

            $sign = hash_hmac('SHA256', $toSign, APP_KEY);

            $sign_enc = Utils::base64url_encode($sign);
            return $toSign . '.' . $sign_enc;
        }catch(Exception $e){
            throw $e;
        }
    }

    public static function decode(string $token){
        $data = explode('.', $token, 3);
        $payload= $data[0];
        $body= $data[1];
        // $payload= Utils::base64url_decode($data[0]);
        // $body= Utils::base64url_decode($data[1]);
        $sign= Utils::base64url_decode($data[2]);


        if($token == "" || count($data) != 3) return ['error' => 'Unauthorized'];

        $verify = self::verifySign($payload, $body, $sign);

        if($verify == 1){
            $body= Utils::base64url_decode($data[1]);
            $body = json_decode($body, true);
            $body = json_decode($body['data'], true);
            return $body;
        }elseif($verify == 3){
            return ['error' => 'Unauthorized'];
        }elseif($verify == 4)
            return ['error' => 'Token has expired'];

        return ['error' => "invalid token"];
    }

    private static function verifySign(string $payload, string $data, string $sign) : int{
        $body = Utils::base64url_decode($data);
        $pay = Utils::base64url_decode($payload);
        $body_json = json_decode($body, true);
        $payload_json = json_decode($pay, true);
        $body_json['data'] = json_decode($body_json['data'], true);
        
        if(isset($body_json['iat'], $body_json['exp'], $body_json['data'], $body_json['iss']) && $body_json['iss'] == $body_json['data']['email'])//)$body_json->iss
        {
            $iat = new DateTime();
            $iat->setTimestamp($body_json['iat']);
            $iat->setTimezone(new DateTimeZone('america/sao_paulo'));
    
            $exp = new DateTime();
            $exp->setTimestamp($body_json['exp']);
            $exp->setTimezone(new DateTimeZone('america/sao_paulo'));

            // $body_enc = Utils::base64url_encode(json_encode($body_json));
            // $payload_enc = Utils::base64url_encode(json_encode($payload_json));
    
            $vSign = '';
            $toSign = $payload . '.' . $data;
            if($payload_json['alg'] === 'HS256')
                $vSign= hash_hmac("SHA256", $toSign, APP_KEY);
            
            $now = new DateTime('now', new DateTimeZone('america/sao_paulo'));

            if(($now->getTimestamp() - $iat->getTimestamp()) <= 0) return 0;
            elseif(($exp->getTimestamp() - $now->getTimestamp()) <= 0) return 4;
            elseif($vSign != $sign) return 3;
            else return 1;
        }
        
        return 0;
    }
}