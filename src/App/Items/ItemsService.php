<?php

namespace App\Items;

use App\Items\ItemsRepository;
use App\Interfaces\Model;
use App\Interfaces\ServicesInterface;
use App\Items\Items;
use controllers\Http\Autenticated;
use DateTime;

class ItemsService implements ServicesInterface{

    private ItemsRepository $repository;
    public function __construct(ItemsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data): Model | array | null
    {
        if(isset($data['user'])){
            $validation = Items::validate($data);
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

    public function getInflow(DateTime $start_date, DateTime $finish_date){
        $data = $this->repository->getInflow($start_date->format('Ymd'), $finish_date->format('Ymd'), Autenticated::getUserAuth()['id']);
        return $data;
    }

    public function getOutflow(DateTime $start_date, DateTime $finish_date){
        $data = $this->repository->getOutflow($start_date->format('Ymd'), $finish_date->format('Ymd'), Autenticated::getUserAuth()['id']);
        return $data;
    }

    public function getAmounts(DateTime $start_date, DateTime $finish_date){
        $data = $this->repository->getAmounts($start_date->format('Ymd'), $finish_date->format('Ymd'), Autenticated::getUserAuth()['id']);
        return $data;
    }
}