<?php

namespace App\Utils\Http;

enum HttpStatus: int
{
    case HTTP_OK = 200;
    case HTTP_CREATED = 201;
    case HTTP_NO_CONTENT = 204;
    case HTTP_BAD_REQUEST = 400;
    case HTTP_UNAUTHORIZED = 401;
    case HTTP_FORBIDDEN = 403;
    case HTTP_NOT_FOUND = 404;
    case HTTP_INTERNAL_SERVER_ERROR = 500;
}

define("HTTP_OK", 200);
define("HTTP_CREATED", 201);
define("HTTP_NO_CONTENT", 204);
define("HTTP_BAD_REQUEST", 400);
define("HTTP_UNAUTHORIZED", 401);
define("HTTP_FORBIDDEN", 403);
define("HTTP_NOT_FOUND", 404);
define("HTTP_INTERNAL_SERVER_ERROR", 500);
