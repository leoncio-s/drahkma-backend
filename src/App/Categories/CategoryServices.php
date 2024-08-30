<?php

namespace App\Services;

use App\Enums\CategoryFilterEnum;
use App\Interfaces\ServiceInterface;
use App\Models\Categories;
use App\Models\User;
use App\Repository\CategoryRepository;
use Exception;

class CategoryServices implements ServiceInterface{


    private CategoryRepository $repository;

    public function __construct(CategoryRepository $repository) {
        $this->repository = $repository;
    }

    public function create(array $data) : Categories | null{
        if($data['user'] instanceof User){
            $data['user'] = $data['user']->id;
            return $this->repository->save($data);
        }
        throw new Exception("Object User is required", 999);
    }

    public function read(User $user, ...$object){
        // $data = null;
        $data = $this->repository->getByUser($user->getId());

        switch (sizeof($object)){
            case 1:
                $filter = $object[0];
                $obj = $this->repository->getByUser($user->getId());
                $data =  $this->retDataFilter($obj, $filter);
                break;
            case 3:
                $filter = $object[0];
                $limit = $object[1];
                $obj = $this->repository->getByUser($user->getId());
                $data = $this->retDataFilter($obj, $filter, $limit);
            default:
                break;
        }
        return $data;
    }

    public function update(array $data) : Categories | null{
        return $this->repository->update($data);
    }

    public function delete(array $data) : bool | null{
        $cat = $this->repository->getById($data['id']);
        if($cat != null && $cat->user_id == $data['user']->id){
            return $this->repository->delete(['id' => $cat->id]);
        }
        return false;
    }


    private function retDataFilter($categories, $filter, $limit=null){
        switch($filter){
            case CategoryFilterEnum::ID:
                $categories = $categories->orderBy('id');
                break;
            case CategoryFilterEnum::NAME:
                $categories = $categories->orderBy('description');
                break;
            case CategoryFilterEnum::ID_DESC:
                $categories = $categories->orderBy('id', 'desc');
                break;
            case CategoryFilterEnum::NAME_DESC:
                $categories = $categories->orderBy('description', 'desc');
                break;
            default:
                $categories = $categories->orderBy('id');
        }

        $pagination = $categories->simplePaginate($limit == null ? 20 : $limit);
        return $pagination;
    }

}