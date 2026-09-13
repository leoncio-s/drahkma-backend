<?php

namespace App\Features\BankAccounts\Application\Models;

use \Symfony\Component\Validator\Constraints as Assert;

class BankAccountsCreateDTO extends BankAccountsDTO
{
    public function __construct(
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

}