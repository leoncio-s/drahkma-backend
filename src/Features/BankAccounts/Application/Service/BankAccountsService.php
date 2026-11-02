<?php

namespace App\Features\BankAccounts\Application\Service;

use App\Features\BankAccounts\Application\Models\BankAccountsDTO;
use App\Features\BankAccounts\Application\Models\BankAccountsMapper;
use App\Features\BankAccounts\Domain\Repository\BankAccountRepositoryInterface;
use App\Features\BankAccounts\Domain\Entity\BankAccounts;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BankAccountsService
{

    public function __construct(private BankAccountRepositoryInterface $repository)
    {
    }

    public function create(BankAccountsDTO $data) : ?BankAccounts
    {

        if ($data->getUser() != null) {
            $data = BankAccountsMapper::fromDTOToEntity($data);
            $data = $this->repository->save($data);
            return $data;
        }
        return null;
    }

    public function read(int $idUser) : array
    {
        return $this->repository->getByUser($idUser);
    }

    public function update(BankAccountsDTO $data) : ?BankAccounts
    {
        $entity = BankAccountsMapper::fromDTOToEntity($data);
        $ret = $this->repository->update($entity);
        return $ret;
    }

    public function delete(array $data) : bool | array
    {
        if (isset($data['user']) && isset($data['id'])) {
            $entity = $this->repository->getByIdAndUser($data['id'], $data['user']);
            if($entity != null)
            {
                return $this->repository->delete($entity);
            }
            throw new NotFoundHttpException("Bank account not found", code:404);
        }
        return 0;
    }
}
