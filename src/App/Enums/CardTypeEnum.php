<?php

namespace App\Enums;

enum CardTypeEnum{
    case Credit;
    case Debit;
    case VR;
    case VA;
    case Others;
}