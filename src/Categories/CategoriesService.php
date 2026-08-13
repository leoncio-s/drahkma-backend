<?php

namespace App\Categories;

use App\Categories\CategoriesRepository;
use App\Interfaces\Model;
use App\Categories\Categories;
use App\Logging\Log;
use App\Logging\LogTypeEnum;
use Exception;

class CategoriesService
{

    private CategoriesRepository $repository;
    public function __construct(CategoriesRepository $repository)
    {
        $this->repository = $repository;
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
            new Log($e, LogTypeEnum::ERROR);
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
