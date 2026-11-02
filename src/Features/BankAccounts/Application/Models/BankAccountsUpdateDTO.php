<?php

namespace App\Features\BankAccounts\Application\Models;

use \Symfony\Component\Validator\Constraints as Assert;

final class BankAccountsUpdateDTO extends BankAccountsCreateDTO
{
    public function __construct(
                
        #[Assert\Type(type:"digits", groups:["update"])]
        #[Assert\Positive(groups:["update"])]
        #[Assert\NotNull(groups:["update"])]
        protected ?int $id,

        #[Assert\Type(type:"digits", groups:["update"])]
        #[Assert\Positive(groups:["update"])]
        #[Assert\NotNull(groups:["update"])]
        protected ?int $user,

        #[Assert\Length(min:1, max:10)]
        #[Assert\Type(type:"digit")]
        #[Assert\NotNull()]
        protected string $bankCode,
        
        #[Assert\Length(min:3, max:100)]
        #[Assert\NotNull()]
        protected string $bankName,

        #[Assert\Length(min:1, max:8)]
        #[Assert\Type(type:"digit")]
        #[Assert\NotNull()]
        protected string $agency,

        #[Assert\Length(min:1, max:10)]
        #[Assert\Type(type:"digit")]
        #[Assert\NotNull()]
        protected string $accountNumber,

        #[Assert\Type(type:"float")]
        #[Assert\NotNull()]
        protected float $initialBalance = 0.00,
    ) {
    }

    public function setUserId(int $userId) : void
    {
        $this->user = $userId;
    }
}