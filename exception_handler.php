<?php

// namespace App\Exceptions;

use App\Exceptions\EmailInvalidatedException;
use App\Utils\Http\HttpStatus;
use App\Utils\Http\Response;
use App\Exceptions\Errors;
use App\Exceptions\InvalidEmailOrPasswordException;
use App\Exceptions\UserNotFoundException;
use App\Logging\Log;
use App\Logging\LogTypeEnum;



function exceptions_error_handler(Throwable $ex) {
    ob_start();
    header('Content-Type: application/json');
    ob_end_flush();
    $erro = new Errors(message:$ex->getMessage(), code: $ex->getCode(), filename: $ex->getFile(), line: $ex->getLine(), previus:$ex->getPrevious());
    if($ex instanceof EmailInvalidatedException){
        $erro->setMessage($erro->getMessage() . " Enviamos um novo email com o link para confirmação.");
    }
    
    if(!($ex instanceof UserNotFoundException || $ex instanceof EmailInvalidatedException || $ex instanceof InvalidEmailOrPasswordException))
    {
        new Log($erro->toLogReturn(), LogTypeEnum::ERROR);
    }

    try{
        if(is_int($ex->getCode()))
            $code = HttpStatus::tryFrom($ex->getCode());
        else $code=null;
    }catch(Exception $e)
    {
        $code=null;
    }finally
    {
        new Log($ex);
    }

    return Response::json($erro->toUserReturn(), $code == null ? HttpStatus::HTTP_INTERNAL_SERVER_ERROR : $code);
}


set_exception_handler('exceptions_error_handler');
