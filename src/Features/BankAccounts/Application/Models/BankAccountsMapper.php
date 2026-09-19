<?php

namespace App\Features\BankAccounts\Application\Models;

use App\Features\BankAccounts\Domain\Entity\BankAccounts;
use App\Features\BankAccounts\Infrastructure\Persistence\ORMBankAccountEntity;

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
            bankCode:$dto->getBankCode(),
            initialBalance: $dto->getInitialBalance()
        );
    }

    public static function fromEntityToORM(BankAccounts $data) : ORMBankAccountEntity
    {
        return new ORMBankAccountEntity(
            userId: $data->getUser(),
            accountNumber:$data->getAccountNumber(),
            agency:$data->getAgency(),
            bankCode: $data->getBankCode(),
            bankName:$data->getBankName(),
            initialBalance: $data->getInitialBalance(),
            createdAt: $data->getCreatedAt(),
            updatedAt: $data->getUpdatedAt(),
            id: $data->getId()
        );
    }

    public static function fromORMToEntity(ORMBankAccountEntity $data) : BankAccounts
    {
        return new BankAccounts(
            userId: $data->getUser(),
            accountNumber:$data->getAccountNumber(),
            agency:$data->getAgency(),
            bankCode: $data->getBankCode(),
            bankName:$data->getBankName(),
            initialBalance: $data->getInitialBalance(),
            createdAt: $data->getCreatedAt(),
            updatedAt: $data->getUpdatedAt(),
            id: $data->getId()
        );
    }
}