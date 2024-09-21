<?php

namespace App\Cards\Enums;

enum CardTypeEnum : string{
    case Credit = 'Credit';
    case Debit = 'Debit';
    case VR = 'VR';
    case VA = 'VA';
    case Others = 'Others';
}