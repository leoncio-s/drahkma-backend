<?php

namespace App\Utils;

class PasswordUtils{

    public static function encoder(string $password) : string {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function compare(string $password, string $hash): bool{
        $opt1=password_verify($password, $hash);
        if($opt1)
        {
            return $opt1;
        }
        return self::old_password_hash_pbkdf2($password) === $hash;
    }

    public static function old_password_hash_pbkdf2(string $password) : string
    {
        $int = 100000;

        $salt = $_ENV["APP_KEY"];
        return bin2hex(hash_pbkdf2("sha256", $password, $salt, $int, binary:true));
    }
}