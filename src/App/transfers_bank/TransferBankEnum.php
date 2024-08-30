<?php

namespace App\Enums;

enum TransferBankEnum:String
{
    case PIX = 'PIX';
    case TED = 'TED';
    case DOC = 'DOC';
    case BOLETO = 'BOLETO';
    case OTHERS = 'OTHERS';
}