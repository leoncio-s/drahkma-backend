<?php

namespace App\Features\BankAccounts\Application\Models;

abstract class BankAccountsDTO
{
    protected string $bankCode;
    protected string $bankName;
    protected string $agency;
    protected string $accountNumber;
    protected float $initialBalance = 0.00;
    protected ?int $user = null;
    protected ?int $id = null;

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

    public function setUser(int $idUser) :void
    {
        $this->user = $idUser;
    }
}