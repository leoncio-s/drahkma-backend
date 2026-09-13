<?php

namespace App\Features\BankAccounts\Infrastructure\Persistence;

use App\Features\BankAccounts\Domain\Entity\BankAccounts;
use App\Features\BankAccounts\Infrastructure\Persistence\BankAccountsRepository;
use DateTime;
use Doctrine\DBAL\Schema\DefaultExpression\CurrentTimestamp;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;

#[ORM\Entity(repositoryClass: BankAccountsRepository::class)]
#[ORM\Table(name:"bank_accounts")]
class ORMBankAccountEntity extends BankAccounts
{
    #[ORM\Id]
    #[ORM\Column(name:'id', type:Types::BIGINT)]
    #[ORM\GeneratedValue]
    public int|null $id;

    #[ORM\Column(name:'bankCode', type:Types::STRING, length:10)]
    public string $bankCode;

    #[ORM\Column(name:'bankName', type:Types::STRING, length:100)]
    public string $bankName;

    #[ORM\Column(name:'agency', type:Types::STRING, length:8)]
    public string $agency;

    #[ORM\Column(name:'accountNumber', type:Types::STRING, length:10)]
    public string $accountNumber;

    #[ORM\Column(name: 'user', type: Types::BIGINT)]
    public int $userId;


    #[ORM\Column(name: 'initial_balance', type: Types::DECIMAL, precision:15, scale:2, options:['default'=>0.00])]
    public float $initialBalance;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, updatable:false, insertable:false, options:['default' => new CurrentTimestamp()])]
    protected DateTime $createdAt;

    #[ORM\Column(name: 'update_at', type: Types::DATETIME_MUTABLE, updatable:false, insertable:false, generated:'ALWAYS')]
    protected ?\DateTime $updatedAt;
}