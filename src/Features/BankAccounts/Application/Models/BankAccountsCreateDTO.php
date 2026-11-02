<?php

namespace App\Features\BankAccounts\Application\Models;

use \Symfony\Component\Validator\Constraints as Assert;

class BankAccountsCreateDTO extends BankAccountsDTO
{
    public function __construct(
        #[Assert\Length(min:1, max:10)]
        #[Assert\Type(type:"string")]
        #[Assert\Regex('/^\d+/')]
        #[Assert\NotNull()]
        protected string $bankCode,
        
        #[Assert\Length(min:3, max:100)]
        #[Assert\NotNull()]
        protected string $bankName,

        #[Assert\Length(min:1, max:8)]
        #[Assert\Type(type:"string")]
        #[Assert\Regex('/^\d+/')]
        #[Assert\NotNull()]
        protected string $agency,

        #[Assert\Length(min:1, max:10)]
        #[Assert\Type(type:"string")]
        #[Assert\Regex('/^\d+/')]
        #[Assert\NotNull()]
        protected string $accountNumber,

        #[Assert\Type(type:"float")]
        #[Assert\NotNull()]
        protected float $initialBalance = 0.00,

        protected ?int $user = null
    ) {
    }

    public static function fromObject(array $data):self
    {
        return new self(
            $data['bankCode'], 
            $data['bankName'], 
            $data['agency'],
            $data['accountNumber'],
            $data['initialBalance'],
            user: $data['user'] ?? null);
    }
}