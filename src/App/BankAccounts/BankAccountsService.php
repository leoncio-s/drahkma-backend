<?php

namespace App\BankAccounts;

use App\BankAccounts\BankAccountsRepository;
use App\Interfaces\Model;
use App\Interfaces\ServicesInterface;
use App\BankAccounts\BankAccounts;
use App\Interfaces\RepositoryInterface;

class BankAccountsService implements ServicesInterface
{

    private BankAccountsRepository $repository;
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data) : Model | array | null
    {

        // new Log($data, LogTypeEnum::DEBUG);
        if (isset($data['user'])) {
            $validation = BankAccounts::validate($data);

            // new Log($validation, LogTypeEnum::DEBUG);
            if (isset($validation['errors'])) {
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

    public function update(array $data) : Model | array | null
    {



        if (isset($data['user'])) {
            $cat = BankAccounts::validate($data, true);
            // new Log($cat, LogTypeEnum::DEBUG);
            if (isset($cat['errors'])) {
                return $cat;
            } else {
                $ret = $this->repository->update($data);
                return $ret;
            }
        }
        return null;
    }

    public function delete(array $data) : bool | int | null | array
    {
        if (isset($data['user']) && isset($data['id'])) {
            return $this->repository->delete($data);
        }
        return 0;
    }
}
