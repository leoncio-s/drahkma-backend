<?php

namespace App\Interfaces;

use App\Interfaces\RepositoryInterface;
use App\Users\User;

interface UserRepositoryInterface extends RepositoryInterface{


    public function get(int $id): User | null;
    public function save(array $data): User | array | null;
    public function update(array $data): User | array | null;
    public function delete(int $data):int | bool | null;
}
