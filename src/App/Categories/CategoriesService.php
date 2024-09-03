<?php

namespace App\Categories;

use App\Categories\CategoriesRepository;
use App\Interfaces\Model;
use App\Interfaces\ServicesInterface;
use App\Categories\Categories;

class CategoriesService implements ServicesInterface{

    private CategoriesRepository $repository;
    public function __construct(CategoriesRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data): Model | array | null
    {
        if(isset($data['user'])){
            $validation = Categories::validate($data);
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