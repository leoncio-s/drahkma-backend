<?php

namespace App\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Items;
use App\Repository\ItemRepository;

class ItemServices implements ServiceInterface{

    private ItemRepository $repository;

    public function __construct(ItemRepository $repository){
        $this->repository = $repository;
    }

    public function create(array $data) : Items | null{
        if($data['user'] != null){
            return $this->repository->save($data);
        }
        return null;
    }
    
    public function read(int $id, string $user) : Items | null {
        $item = $this->repository->getByIdAndUser($id, $user);
        return $item;
    }

    public function update(array $data) : Items | null{
        return $this->repository->update($data);
    }

    public function delete($data) : bool | null{
        return $this->repository->delete($data);
    }
}