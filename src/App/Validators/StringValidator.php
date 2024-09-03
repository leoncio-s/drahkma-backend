<?php

namespace App\Validators;

class StringValidator {
    public static function descrValidate(string $value) : bool{
        return preg_match("/[\w\s]+/", $value) && !self::sqlInjection($value);
    }

    public static function namesValidate(string $value):bool{
        return !str_starts_with("'", $value) && !str_ends_with("'", $value) && preg_match("/[\\w\\s\\']+/", $value) && !self::sqlInjection($value);
    }

    public static function passwordValidator(string $value) : bool{
        return preg_match("/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,20}$/", $value);
    }

    public static function phoneNumbersValidator(string $value): bool {
        return preg_match("/[\d\+]{8,14}/", $value);
    }

    public static function emailValidator(string $value) : bool {
        return strlen($value) > 4 && strlen($value) <= 150 && filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    public static function cardsDigitsValidator(string $value) : bool {
        return preg_match("/[0-9]{4}/", $value);
    }

    public static function onlyNumbers(string $value) : bool
    {
        return preg_match("/[0-9]+/", $value);
    }

    public static function sqlInjection(string $value) : bool {
        return preg_match("/(((\+)|(\ ))(((\%27)|(\'))|union|select|delete|insert|or|alter|drop|and)(((\+)|(\ ))))/", $value);
    }
}