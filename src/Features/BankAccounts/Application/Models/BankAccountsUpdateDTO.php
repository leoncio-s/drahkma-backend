<?php

namespace App\Features\BankAccounts\Application\Models;

use \Symfony\Component\Validator\Constraints as Assert;

final readonly class BankAccountsUpdateDTO extends BankAccountsDTO
{
    public function __construct(
                
        #[Assert\Type(type:"digits", groups:["update"])]
        #[Assert\Positive(groups:["update"])]
        #[Assert\NotNull(groups:["update"])]
        public ?int $id,

        #[Assert\Type(type:"digits", groups:["update"])]
        #[Assert\Positive(groups:["update"])]
        #[Assert\NotNull(groups:["update"])]
        public ?int $user,

        #[Assert\Length(min:1, max:10)]
        #[Assert\Type(type:"digit")]
        #[Assert\NotNull()]
        public string $bankCode,
        
        #[Assert\Length(min:3, max:100)]
        #[Assert\NotNull()]
        public string $bankName,

        #[Assert\Length(min:1, max:8)]
        #[Assert\Type(type:"digit")]
        #[Assert\NotNull()]
        public string $agency,

        #[Assert\Length(min:1, max:8)]
        #[Assert\Type(type:"digit")]
        #[Assert\NotNull()]
        public string $accountNumber
    ) {
    }

    public function setUserId(int $userId) : void
    {
        $this->user = $userId;
    }
}