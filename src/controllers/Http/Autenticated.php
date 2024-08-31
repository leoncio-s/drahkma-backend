<?php

namespace controllers\Http;

use App\Users\User;
use App\Utils\JWTTokenUtils;

class Autenticated{
    private static $user;

    public static function autenticated(){
        $auth = $_SERVER['HTTP_AUTHORIZATION'];
    
        $token = str_replace('Bearer ', '', $auth);

        $validate = JWTTokenUtils::decode($token);
        if(isset($validate['error'])){
            return Response::json($validate, HttpStatus::HTTP_UNAUTHORIZED);
        }
        self::$user = $validate;

        return true;
    }

    public static function getUserAuth(){
        return self::$user;
    }
}
