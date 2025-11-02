<?php

namespace App\Utils\Http;

class Response
{
    public static function json(?array $data = [], HttpStatus|int $http_code_status = HttpStatus::HTTP_OK): void
    {
        ob_start();
        http_response_code((gettype($http_code_status) == 'integer') ? $http_code_status : $http_code_status->value);
        ob_end_flush();
        echo (json_encode($data));
    }
}
