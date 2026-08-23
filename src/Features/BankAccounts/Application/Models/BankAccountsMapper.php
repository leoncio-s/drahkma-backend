<?php

namespace App\Features\BankAccounts\Application\Models;

use App\Features\BankAccounts\Domain\Entity\BankAccounts;

class BankAccountsMapper
{
    public static function fromDTOToEntity(BankAccountsDTO $dto) : BankAccounts
    {
        return new BankAccounts(
            id: $dto->getId(),
            userId: $dto->getUser(),
            bankName: $dto->getBankName(),
            agency:$dto->getAgency(),
            accountNumber:$dto->getAccountNumber(),
            bankCode:$dto->getBankCode()
        );
    }
}