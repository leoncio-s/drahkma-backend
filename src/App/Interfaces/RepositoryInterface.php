<?php

namespace App\Interfaces;

use App\Database\Databases;
use App\Database\MySqlDatabaseImpl;

interface RepositoryInterface{
    public function __construct(MySqlDatabaseImpl $databases);
    // public function get(int $id): Model | null | array;
    // public function save(array $data): Model | null | array;
    // public function update(array $data): Model | null | array;
    // public function delete($data):bool | int | array;
}
