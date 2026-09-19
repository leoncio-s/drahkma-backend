<?php

namespace App\Features\BankAccounts\Application\Models;

use \Symfony\Component\Validator\Constraints as Assert;

final readonly class BankAccountsCreateDTO extends BankAccountsDTO
{
    public function __construct(
        #[Assert\Length(min:1, max:10)]
        #[Assert\Type(type:"string")]
        #[Assert\Regex('/^\d+/')]
        #[Assert\NotNull()]
        public string $bankCode,
        
        #[Assert\Length(min:3, max:100)]
        #[Assert\NotNull()]
        public string $bankName,

        #[Assert\Length(min:1, max:8)]
        #[Assert\Type(type:"string")]
        #[Assert\Regex('/^\d+/')]
        #[Assert\NotNull()]
        public string $agency,

        #[Assert\Length(min:1, max:8)]
        #[Assert\Type(type:"string")]
        #[Assert\Regex('/^\d+/')]
        #[Assert\NotNull()]
        public string $accountNumber,

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
            user: $data['user'] ?? null);
    }
}