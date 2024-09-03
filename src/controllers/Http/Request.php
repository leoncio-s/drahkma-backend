<?php

namespace controllers\Http;

use Exception;

class Request{
    public static function getAll() :  array{
        $get_content = file_get_contents('php://input');
        if($get_content == null){
            return Response::json(["error" => "No body content"], HttpStatus::HTTP_BAD_REQUEST);
        }
        return json_decode($get_content, true);
    }
}