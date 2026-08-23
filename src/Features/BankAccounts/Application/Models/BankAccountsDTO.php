<?php

namespace App\Features\BankAccounts\Application\Models;

abstract class BankAccountsDTO
{
    protected readonly int $id;
    protected readonly int $user;
    protected string $bankCode;
    protected string $bankName;
    protected string $agency;
    protected string $accountNumber;

    public function getId(): ?int 
    {
        return $this->id;
    }

    public function getUser(): ?int 
    {
        return $this->user;
    }

    public function getBankCode(): string 
    {
        return $this->bankCode;
    }

    public function getBankName(): string 
    {
        return $this->bankName;
    }

    public function getAgency(): string 
    {
        return $this->agency;
    }

    public function getAccountNumber(): string 
    {
        return $this->accountNumber;
    }
}