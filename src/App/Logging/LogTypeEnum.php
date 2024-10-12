<?php

namespace App\Logging;

enum LogTypeEnum : string{
    case INFO = '[INFO]';
    case ERROR = '[ERROR]';
    case WARNNING = '[WARNNING]';
    case DEBUG = '[DEBUG]';
}