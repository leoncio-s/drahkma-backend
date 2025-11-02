<?php

namespace App\Exceptions;

use Exception;

class InvalidEmailOrPasswordException extends Exception{
    public function __construct($message="Email ou senha inválidos", $code=400) {
        parent::__construct($message, $code);
    }
}