<?php 

namespace App\Features\BankAccounts\Domain\Entity;

use DateTime;

class BankAccounts
{
    protected int|null $id;
    protected string $bankCode;
    protected int|null $user;
    protected string $bankName;
    protected string $agency;
    protected string $accountNumber;
    protected DateTime $createdAt;
    protected ?\DateTime $updatedAt;
}