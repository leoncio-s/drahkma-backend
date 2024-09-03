<?php

namespace App\Users;

use App\Users\User;

interface UserRepositoryInterface{

    public function get(int $id): User | null;
    public function save(array $data): User | array | null;
    public function update(array $data): User | array | null;
    public function delete(int $data):int | bool | null;
}
