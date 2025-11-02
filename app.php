<?php

use App\Exceptions\EmailInvalidatedException;
use App\Exceptions\Errors;
use App\Logging\Log;
use App\Logging\LogTypeEnum;
use App\Utils\Http\HttpStatus;
use App\Utils\Http\Response;

// use function App\Exceptions\exceptions_error_handler as errors;

// exceptions_error_handler();

spl_autoload_register(function($className){
    include __DIR__ . "/src/" . str_replace("\\", "/", $className) . '.php';
});
