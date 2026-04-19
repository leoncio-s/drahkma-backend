<?php

namespace App\Utils\Http;

use App\Logging\Log;
use App\Logging\LogTypeEnum;
use Exception;

class Request{
    public static function getAll() :  array | null{
        if($_SERVER['CONTENT_TYPE'] != 'application/json; charset=utf-8' && $_SERVER['CONTENT_TYPE'] != 'application/json'){
            return Response::json(http_code_status:HttpStatus::HTTP_BAD_REQUEST);
        }
        $get_content = file_get_contents('php://input');
        if($get_content == null){
            return Response::json(["error" => "No body content"], HttpStatus::HTTP_BAD_REQUEST);
        }
        try{
            return json_decode($get_content, true);
        }catch(Exception $e){
            new Log($e, LogTypeEnum::INFO);
            return Response::json(["error" => "INTERNAL SERVER ERROR"], HttpStatus::HTTP_INTERNAL_SERVER_ERROR);
        } 
    }
}