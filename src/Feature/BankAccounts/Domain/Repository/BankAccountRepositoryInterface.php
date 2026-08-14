<?php

namespace App\Feature\BankAccounts\Domain\Repository;

use App\Database\MySqlDatabaseImpl;

interface BankAccountRepositoryInterface{
    public function __construct(MySqlDatabaseImpl $databases);
    // public function get(int $id): Model | null | array;
    // public function save(array $data): Model | null | array;
    // public function update(array $data): Model | null | array;
    // public function delete($data):bool | int | array;
}
