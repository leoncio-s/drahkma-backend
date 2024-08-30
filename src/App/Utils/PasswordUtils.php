<?php

namespace App\Utils;

class PasswordUtils{

    public static function encoder(string $password) : string {
        $int = 100000;

        $salt = APP_KEY;
        return bin2hex(hash_pbkdf2("sha256", $password, $salt, $int, binary:true));
    }

    public static function compare(string $password, string $hash): bool{
        return self::encoder($password) === $hash;
    }
}