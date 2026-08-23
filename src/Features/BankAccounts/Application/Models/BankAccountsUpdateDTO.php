<?php

namespace App\Features\BankAccounts\Application\Models;

use \Symfony\Component\Validator\Constraints as Assert;

class BankAccountsUpdateDTO extends BankAccountsDTO
{
    public function __construct(
                
        #[Assert\Type(type:"digits", groups:["update"])]
        #[Assert\Positive(groups:["update"])]
        #[Assert\NotNull(groups:["update"])]
        protected readonly int $id,

        #[Assert\Type(type:"digits", groups:["update"])]
        #[Assert\Positive(groups:["update"])]
        #[Assert\NotNull(groups:["update"])]
        protected readonly int $user,

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

        #[Assert\Length(min:1, max:8)]
        #[Assert\Type(type:"digit")]
        #[Assert\NotNull()]
        protected string $accountNumber
    ) {
    }

}