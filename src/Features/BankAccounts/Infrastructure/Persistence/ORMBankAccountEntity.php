<?php

namespace App\Features\BankAccounts\Infrastructure\Persistence;

use App\Features\BankAccounts\Infrastructure\Persistence\BankAccountsRepository;
use App\Validators\StringValidator;
use DateTime;
use Doctrine\DBAL\Schema\DefaultExpression\CurrentTimestamp;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BankAccountsRepository::class)]
#[ORM\Table(name:"bank_accounts")]
final class ORMBankAccountEntity
{
    public function __construct(
        #[ORM\Column(name:'bankCode', type:Types::STRING, length:10)]
        private string $bankCode,

        #[ORM\Column(name:'bankName', type:Types::STRING, length:100)]
        private string $bankName,

        #[ORM\Column(name:'agency', type:Types::STRING, length:8)]
        private string $agency,

        #[ORM\Column(name:'accountNumber', type:Types::STRING, length:10)]
        private string $accountNumber,

        #[ORM\Column(name: 'user', type: Types::BIGINT)]
        private int $userId,


        #[ORM\Column(name: 'initial_balance', type: Types::DECIMAL, precision:15, scale:2, options:['default'=>0.00])]
        private float $initialBalance,

        #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, updatable:false, insertable:false, options:['default' => new CurrentTimestamp()])]
        private ?DateTime $createdAt = null,

        #[ORM\Column(name: 'update_at', type: Types::DATETIME_MUTABLE, updatable:false, insertable:false, generated:'ALWAYS')]
        private ?\DateTime $updatedAt = null,

        #[ORM\Id]
        #[ORM\Column(name:'id', type:Types::BIGINT)]
        #[ORM\GeneratedValue]
        private int|null $id = null,
    ){}

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

    public function setId(?int $id) : void {
        $this->id = ($id > 0)? $id : null;
    }

    public function setBankCode(?string $bankCode) : void{
        
        $this->bankCode = ($bankCode != null && StringValidator::onlyNumbers($bankCode)) ?  $bankCode : null;
    }

    public function setUser(?int $userId) : void{
        $this->userId = $userId;
    }

    public function setBankName(?string $bankName) : void{
        $this->bankName = ($bankName != null && StringValidator::descrValidate($bankName)) ? $bankName : null;
    }

    public function setAgency(?string $agency) : void{
        $this->agency = ($agency != null && StringValidator::onlyNumbers($agency)) ? $agency : null;
    }

    public function setAccountNumber(?string $accountNumber) : void{
        $this->accountNumber = ($accountNumber != null && StringValidator::onlyNumbers($accountNumber)) ? $accountNumber : null;
    }

    public function setInitialBalance(?float $value)
    {
        $this->initialBalance = ($value != null) ? $value : 0.00;
    }
}