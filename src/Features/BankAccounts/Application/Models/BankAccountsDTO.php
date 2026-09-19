<?php

namespace App\Features\BankAccounts\Application\Models;

readonly abstract class BankAccountsDTO
{
    protected string $bankCode;
    protected string $bankName;
    protected string $agency;
    protected string $accountNumber;
    protected float $initialBalance;
    protected ?int $user;
    protected ?int $id;

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

    public function getInitialBalance(): float 
    {
        return $this->initialBalance;
    }
}