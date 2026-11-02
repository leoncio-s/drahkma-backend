<?php

namespace App\Features\BankAccounts\Domain\Repository;

use App\Features\BankAccounts\Domain\Entity\BankAccounts;

interface BankAccountRepositoryInterface{
    public function get(int $id): ?BankAccounts;
    public function save(BankAccounts $data): ?BankAccounts;
    public function update(BankAccounts $data): ?BankAccounts;
    public function delete(BankAccounts $data):bool | int | array;
    public function getByIdAndUser(int $id, int $userId): ?BankAccounts;
    public function getByUser(int $userId): array;
}
