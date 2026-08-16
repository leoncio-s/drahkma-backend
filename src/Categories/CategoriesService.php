<?php

namespace App\Categories;

use App\Categories\CategoriesRepository;
use App\Interfaces\Model;
use App\Categories\Categories;
use Exception;
use Psr\Log\LoggerInterface;

class CategoriesService
{

    public function __construct(private CategoriesRepository $repository, private LoggerInterface $logger)
    {
    }

    public function create(array $data) : Model | array | null
    {
        if (isset($data['user'])) {
            $validation = Categories::validate($data);
            if (isset($validation['errors'])) {
                return $validation;
            }
            $data = $this->repository->save($data);
            return $data;
        }
        return null;
    }

    public function read(int $idUser): ?array
    {
        return $this->repository->getByUser($idUser);
    }

    public function readAllByUser(int $idUser): ?array
    {
        return $this->repository->getAllByUser($idUser);
    }

    public function update(array $data) : Model | array | null
    {
        try{
            if (isset($data['user'])) {
                // $data['user'] = $data['user'];
                $cat = Categories::validate($data, true);
                if (isset($cat['errors'])) {
                    return $cat;
                } else {
                    $ret = $this->repository->update($cat['data']);
                    return $ret;
                }
            }
            return null;
        }catch(Exception $e)
        {
            $this->logger->error($e->getMessage(), $e->getTrace());
            throw new Exception("Erro ao processar a solicitação", 500, $e);
        }
    }

    public function delete(array $data) : bool | int | null | array
    {
        if (isset($data['user']) && isset($data['id'])) {
            return $this->repository->delete($data);
        }
        return 0;
    }
}
