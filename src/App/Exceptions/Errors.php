<?php

namespace App\Exceptions;

use \DateTime;
use \Throwable;

class Errors{
    private string $message;
    private int $code;
    private string $filename;
    private int $line;
    private Throwable|null $previus;

    public function __construct(string $message, int $code, string $filename, int $line, Throwable|null $previus){
        $this->message = $message;
        $this->code = $code;
        $this->filename = $filename;
        $this->line = $line;
        $this->previus = $previus;
    }

    public function setMessage(string $message){
        $this->message = $message;
    }

    public function getMessage() : string{
        return $this->message;
    }

    
    public function toUserReturn():array{
        return [
            "date" => new DateTime("now"),
            "error" => $this->message,
            "code" => $this->code
        ];
    }

    public function toLogReturn():array{
        return [
            "date" => new DateTime("now"),
            "error" => $this->message,
            "code" => $this->code,
            "filename" => $this->filename,
            "line" => $this->line,
            "prev" => $this->previus->getTraceAsString()
        ];
    }

}