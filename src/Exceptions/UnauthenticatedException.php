<?php
namespace App\Exceptions;

use Exception;

class UnauthenticatedException extends Exception
{
    public function __construct($message='Não autenticado') {
        parent::__construct($message, 401);
    }
}