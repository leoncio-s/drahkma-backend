<?php

// namespace App\Exceptions;

use App\Exceptions\EmailInvalidatedException;
use App\Utils\Http\HttpStatus;
use App\Utils\Http\Response;
use App\Exceptions\Errors;
use App\Logging\Log;
use App\Logging\LogTypeEnum;
use \Throwable;



function exceptions_error_handler(Throwable $ex) {
    header('Content-Type: application/json');
    $erro = new Errors(message:$ex->getMessage(), code: $ex->getCode(), filename: $ex->getFile(), line: $ex->getLine(), previus:$ex->getPrevious());
    if($ex instanceof EmailInvalidatedException){
        $erro->setMessage($erro->getMessage() . " Enviamos um novo email com o link para confirmação.");
    }

    $code = HttpStatus::tryFrom(400);

    new Log($erro->toLogReturn(), LogTypeEnum::ERROR);

    return Response::json($erro->toUserReturn(), $code == null ? HttpStatus::HTTP_INTERNAL_SERVER_ERROR : $code);
}


set_exception_handler('exceptions_error_handler');
