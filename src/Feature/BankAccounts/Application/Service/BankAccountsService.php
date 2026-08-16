<?php

namespace App\Feature\BankAccounts\Application\Service;

use App\Feature\BankAccounts\Domain\Repository\BankAccountRepositoryInterface;
use App\Feature\BankAccounts\Infrastructure\Persistence\BankAccountsRepository;
use App\Interfaces\Model;
use App\Feature\BankAccounts\Domain\Entity\BankAccounts;

class BankAccountsService
{

    private BankAccountsRepository $repository;
    public function __construct(BankAccountRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data) : Model | array | null
    {

        if (isset($data['user'])) {
            $validation = BankAccounts::validate($data);

            if (isset($validation['errors'])) {
                return $validation;
            }
            $data = $this->repository->save($data);
            return $data;
        }
        return null;
    }

    public function read(int $idUser) : ?array
    {
        return $this->repository->getByUser($idUser);
    }

    public function update(array $data) : Model | array | null
    {



        if (isset($data['user'])) {
            $cat = BankAccounts::validate($data, true);
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
