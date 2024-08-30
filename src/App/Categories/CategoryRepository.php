<?php

namespace App\Repository;

use App\Interfaces\Repository;
use App\Interfaces\RepositoryInterface;
use App\Models\Categories;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Model;

class CategoryRepository implements RepositoryInterface
{
    public static function save(array $data):Categories{

        if($data['user'] != null){
            $category = Categories::firstOrNew(
                ['description' => strtoupper($data['description']), 'user_id' => $data['user']]
            );
            
            $category->save();
            return $category;
        }
        throw new Exception("Object User is required", 999);
    }

    public static function update(array $data):Categories{
        $category = Categories::where('id', $data['id'])->get();
        $category->description = $data['description'];
        $category->refresh();
        return $category;
    }

    public static function get(int $id): ?Model
    {
        return Categories::where('id', $id)->get();
    }

    public static function delete($data):bool{
        if(Categories::where('id', $data['id'])->delete() >= 1){
            return true;
        }
        return false;
    }

    public static function getByUser(int $user){
        return Categories::where('user_id', $user);
    }

    public static function getById(int $id){
        return Categories::where('id', $id)->get();
    }
}