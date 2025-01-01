<?php

namespace App\Utils;

class Base64Utils
{


    public static function base64_url_encode(string $data): string
    {
        $base = base64_encode($data);

        return urlencode($base);
    }


    public static function base64_url_decode(string $data): string
    {
        $base = base64_decode($data);

        return urldecode($base);
    }


    /// https://base64.guru/developers/php/examples/base64url#:~:text=PHP%20doesn%E2%80%99t%20support%20the%20Base64URL%20standard%2C%20but%20you,instead%20of%20%E2%80%9C%2B%E2%80%9D%20and%20%E2%80%9C_%E2%80%9D%20instead%20of%20%E2%80%9C%2F%E2%80%9D.
    public static function base64url_encode($data)
    {
        // First of all you should encode $data to Base64 string
        $b64 = base64_encode($data);

        // Make sure you get a valid result, otherwise, return FALSE, as the base64_encode() function do
        if ($b64 === false) {
            return false;
        }

        // Convert Base64 to Base64URL by replacing “+” with “-” and “/” with “_”
        $url = strtr($b64, '+/', '-_');

        // Remove padding character from the end of line and return the Base64URL result
        return rtrim($url, '=');
    }

    /**
     * Decode data from Base64URL
     * @param string $data
     * @param boolean $strict
     * @return boolean|string
     */
    public static function base64url_decode($data, $strict = false)
    {
        // Convert Base64URL to Base64 by replacing “-” with “+” and “_” with “/”
        $b64 = strtr($data, '-_', '+/');

        // Decode Base64 string and return the original data
        return base64_decode($b64, $strict);
    }
}
