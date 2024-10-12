<?php

namespace App\Cards;

use App\Interfaces\Model;
use App\Interfaces\ServicesInterface;
use App\Cards\Cards;

class CardsService implements ServicesInterface{

    private CardsRepository $repository;
    public function __construct(CardsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data): Model | array | null
    {
        if(isset($data['user'])){
            $validation = Cards::validate($data);
            if(isset($validation['errors'])){
                return $validation;
            }
            $data = $this->repository->save($validation['data']);
            return $data;
        }
    }

    public function read(int $idUser) : ?array
    {
        return $this->repository->getByUser($idUser);
    }

    public function update(array $data): array | Model | null
    {
        if(isset($data['user'])){
            $ret = Cards::validate($data);
            if(!isset($ret["errors"])){
                return $this->repository->update($data);
            }
            return $ret;
        }
    }

    public function delete(array $data): bool | int | null
    {
        if(isset($data['user'])){
            $data['user'] = $data['user'];
            return $this->repository->delete($data);
            // return 0;
        }
        return 0;
    }
}