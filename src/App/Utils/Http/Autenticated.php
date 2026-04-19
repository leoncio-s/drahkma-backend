<?php

namespace App\Utils\Http;

use App\Exceptions\UnauthenticatedException;
use App\Utils\JWTTokenUtils;

class Autenticated{
    private static $user;

    public static function autenticated(){
        if(isset($_SERVER['HTTP_AUTHORIZATION']))
        {
            $auth = $_SERVER['HTTP_AUTHORIZATION'];
            if($auth != null){
                $token = str_replace('Bearer ', '', $auth);

                $validate = JWTTokenUtils::decode($token);
                if(isset($validate['error'])){
                    throw new UnauthenticatedException($validate['error']);
                }
                self::$user = $validate;

                return true;
            }

            throw new UnauthenticatedException();
        }
        throw new UnauthenticatedException();
    }

    public static function getUserAuth(){
        return self::$user;
    }
}
