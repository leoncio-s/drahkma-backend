<?php

namespace App\Services;

use App\Interfaces\ServiceInterface;
use App\Models\User;
use App\Repository\BankAccountsRepository;
use App\Interfaces\Model;

class BankAccountsService implements ServiceInterface{

    private BankAccountsRepository $repository;
    public function __construct(BankAccountsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data): ?Model
    {
        if(isset($data['user'])){
            $data['user'] = $data['user'];
            return $this->repository->save($data);
        }
    }

    public function read($data) : array | Model | null
    {
        return null;
    }

    public function update(array $data): ?Model
    {
        if(isset($data['user'])){
            $data['user'] = $data['user'];
            return $this->repository->update($data);
        }
    }

    public function delete(array $data): bool | int | null
    {
        if(isset($data['user'])){
            $data['user'] = $data['user'];
            // return $this->repository->delete($data);
            return 0;
        }
        return 0;
    }
}