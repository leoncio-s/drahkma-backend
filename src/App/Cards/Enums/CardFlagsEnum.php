<?php

namespace App\Cards\Enums;

enum CardFlagsEnum:String{
    case Visa = 'Visa';
    case Mastercard = 'Mastercard';
    case Elo = 'Elo';
    case AExp = 'American_Express'; // American Express
    case Hipercard = 'Hipercard';
    case Others = 'Others';
}