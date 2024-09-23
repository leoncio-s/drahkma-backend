<?php

namespace App\BankAccounts;

use App\BankAccounts\BankAccountsRepository;
use App\Interfaces\Model;
use App\Interfaces\ServicesInterface;
use App\BankAccounts\BankAccounts;

class BankAccountsService implements ServicesInterface{

    private BankAccountsRepository $repository;
    public function __construct(BankAccountsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data): Model | array | null
    {
        if(isset($data['user'])){
            $validation = BankAccounts::validate($data);
            if(isset($validation['errors'])){
                return $validation;
            }
            $data = $this->repository->save($data);
            return $data;
            
        }
    }

    public function read(int $idUser) : ?array
    {
        return $this->repository->getByUser($idUser);
    }

    public function update(array $data): Model | array | null
    {
        if(isset($data['user'])){
            // $data['user'] = $data['user'];
            $cat = BankAccounts::validate($data, update:true);
            if(isset($cat['errors'])){
                return $cat;
            }else{
                $ret = $this->repository->update($data);
                return $ret;
            }
            
        }
        return null;
    }

    public function delete(array $data): bool | int | null | array
    {
        if(isset($data['user']) && isset($data['id'])){
            return $this->repository->delete($data);
        }
        return 0;
    }
}