<?php

namespace controllers\Http;

use App\Users\User;
use App\Utils\JWTTokenUtils;

class Autenticated{
    private static $user;

    public static function autenticated(){
        $auth = $_SERVER['HTTP_AUTHORIZATION'];
        if($auth != null){
            $token = str_replace('Bearer ', '', $auth);

            $validate = JWTTokenUtils::decode($token);
            if(isset($validate['error'])){
                return Response::json($validate, HttpStatus::HTTP_UNAUTHORIZED);
            }
            self::$user = $validate;

            return true;
        }

        return Response::json([], HttpStatus::HTTP_FORBIDDEN);
    }

    public static function getUserAuth(){
        return self::$user;
    }
}
