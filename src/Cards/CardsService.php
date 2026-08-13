<?php

namespace App\Cards;

use App\Interfaces\Model;
use App\Cards\Cards;
use Exception;

class CardsService
{

    private CardsRepository $repository;
    public function __construct(CardsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data) : Model | array | null
    {
        if (isset($data['user'])) {
            $validation = Cards::validate($data);
            if (isset($validation['errors'])) {
                return $validation;
            }
            $data = $this->repository->save($validation['data']);
            return $data;
        }
        return null;
    }

    public function read(int $idUser) : ?array
    {
        return $this->repository->getByUser($idUser);
    }

    public function update(array $data) : array | Model | null
    {
        if (isset($data['user'])) {
            $ret = Cards::validate($data);
            if (!isset($ret["errors"])) {
                return $this->repository->update($data);
            }
            return $ret;
        }
        return null;
    }

    public function delete(array $data) : bool | int | null | array
    {
        try{
            if (isset($data['user'])) {
                $data['user'] = $data['user'];
                return $this->repository->delete($data);
            }
            return 0;
        }catch(Exception $e)
        {
            throw $e;
        }
    }
}
