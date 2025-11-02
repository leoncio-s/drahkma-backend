<?php

namespace App\Utils;

class GenerateTokensUtils{

    public static function emailGenerateToken($email, $id, $exp): string
    {
        $data = 'lfinanca$' . $id . '$' . $exp;
        $hash = hash_hmac('sha256', $data, $email);

        return $hash;
    }

    public static function generateRandomTokenForgetPassword()
    {
        $alfa =  array_merge_recursive(range('A', 'Z'), range(0,9));
        $maxLength = 6;
        $code = "";
        
        for($x = 0; $x < $maxLength; $x++)
        {
            $randomChoice = random_int(0, count($alfa) - 1);
            $code .= $alfa[$randomChoice];
        }

        return $code;

    }
}