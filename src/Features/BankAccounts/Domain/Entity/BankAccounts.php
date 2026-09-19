<?php 

namespace App\Features\BankAccounts\Domain\Entity;

use DateTime;

class BankAccounts
{
    public function __construct(
        protected string $bankCode,
        protected string $bankName,
        protected string $agency,
        protected string $accountNumber,
        protected float $initialBalance,
        protected ?DateTime $createdAt = null,
        protected ?\DateTime $updatedAt = null,
        protected ?int $id = null,
        protected ?int $userId = null,
    ) {}


    public function getId() : int|null
    { 
        return $this->id;
    }

    public function getBankCode() : string
    {
        return $this->bankCode;
    }

    public function getBankName() : string
    {
        return $this->bankName;
    }

    public function getAgency() : string
    {
        return $this->agency;
    }

    public function getAccountNumber() : string
    {
        return $this->accountNumber;
    }

    public function getUser() : int|null
    {
        return $this->userId;
    }

    public function getCreatedAt() : ?DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt() : ?DateTime
    {
        return $this->updatedAt;
    }

    public function getInitialBalance() : float
    {
        return $this->initialBalance;
    }

}