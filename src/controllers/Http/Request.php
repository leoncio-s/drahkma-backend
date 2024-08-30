<?php

namespace controllers\Http;

class Request{
    public static function getAll() :  array{
        $get_content = file_get_contents('php://input');

        return json_decode($get_content, true);
    }
}