<?php

namespace App\Logging;

use DateTime;
use DateTimeZone;

class Log{
    private String $data;
    private LogTypeEnum $type;
    public function __construct($data, LogTypeEnum $type = LogTypeEnum::DEBUG) {
        $this->data = (string) var_export($data, true);
        $this->type = $type;
        $this->write($data);
    }

    private function write(){
        $date = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));

        $path = "./log/";

        if(!file_exists($path)){
            mkdir($path, permissions: 0755);
        }
        $file = $path . $date->format('Y-m-d') . '.log';
        $text = $date->format('Y-m-d H:i:sP')  . ' ' . $this->type->value . ' ' . $this->data . PHP_EOL;
        file_put_contents(filename:$file, data:$text, flags:FILE_APPEND | LOCK_EX);
        // fclose($file);
    }
}