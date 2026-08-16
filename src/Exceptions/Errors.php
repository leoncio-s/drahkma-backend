<?php

namespace App\Exceptions;

use \DateTime;
use DateTimeZone;
use \Throwable;

class Errors{
    private string $message;
    private int|string $code;
    private string $filename;
    private int $line;
    private Throwable|null $previus;

    public function __construct(string $message, int|string $code, string $filename, int $line, Throwable|null $previus){
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

    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    
    public function toUserReturn():array{
        return [
            "date" => (new DateTime("now", new DateTimeZone("America/Sao_Paulo")))->format("Y-m-d H:i:s P"),
            "message" => $this->message,
            "statusCode" => $this->code
        ];
    }

    public function toLogReturn():array{
        return [
            "date" => (new DateTime("now", new DateTimeZone("America/Sao_Paulo")))->format('Y-m-d H:i:s T'),
            "error" => $this->message,
            "code" => $this->code,
            "filename" => $this->filename,
            "line" => $this->line,
            "prev" => $this->previus != null? $this->previus->getTrace() : []
        ];
    }

}