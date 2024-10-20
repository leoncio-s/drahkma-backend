<?php

namespace App\Items;

enum ItemsGroupEnum:string{
    case Cards = "card";
    case Categories = "category";
    case BankAccounts = "bank_account";
}