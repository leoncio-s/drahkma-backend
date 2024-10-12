<?php

namespace controllers\Http;

use function PHPSTORM_META\type;

class Response{
    public static function json(?array $data = [], HttpStatus|int $http_code_status=HttpStatus::HTTP_OK) : void{
        http_response_code((gettype($http_code_status) == 'integer')? $http_code_status : $http_code_status->value);
        echo(json_encode($data));
    }
}