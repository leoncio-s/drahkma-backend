<?php

namespace App\Feature\BankAccounts\Domain\Repository;

use App\Database\Databases;
use Psr\Log\LoggerInterface;

interface BankAccountRepositoryInterface{
    public function __construct(Databases $databases, LoggerInterface $logger);
    // public function get(int $id): Model | null | array;
    // public function save(array $data): Model | null | array;
    // public function update(array $data): Model | null | array;
    // public function delete($data):bool | int | array;
}
