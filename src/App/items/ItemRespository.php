<?php

namespace App\Repository;

use App\Interfaces\RepositoryInterface;
use App\Models\Categories;
use App\Models\Items;
use App\Models\User;
use Exception;

class ItemRepository implements RepositoryInterface{

    public static function save(array $data):Items | null{

        if($data['user'] != null){
            $item = new Items();
            $item->description = $data['description'];
            $item->expense = boolval($data['expense']);
            $item->value = floatval($data['value']);
            $item->date = date_parse($data['date']);
            $item->category_id = $data['category'];
            if($data["transfer_bank"] != null){
                $item->transfer_bank_id = $data["transfer_bank"];
                $item->card_id = null;
            }else if($data["card"] != null){
                $item->card_id = $data["card"];
                $item->transfer_bank_id = null;
            }
    
            $item->user_id=$data['user'];
    
            $item->save();
    
            return $item;
            
        }
        throw new Exception("Object User is required", 999);
    }
    public static function update(array $data):Items{
        if($data['id'] == null){
            return null;
        }
        $item = Items::where('id', $data['id'])->first();
        $item->description = $data['description'];
        $item->expense = boolval($data['expense']);
        $item->value = floatval($data['value']);
        $item->date = date_parse($data['date']);
        $item->category_id = $data['category'];
        if($data["transfer_bank"] != null){
            $item->transfer_bank_id = $data["transfer_bank"];
            $item->card_id = null;
        }else if($data["card"] != null){
            $item->card_id = $data["card"];
            $item->transfer_bank_id = null;
        }

        $item->save();

        return $item;
    }
    public static function delete($data):bool{
        $item = Items::where(['id' => $data['id'], 'user_id' => $data['user']])->get();

        return $item->delete() >=1;
    }

    public static function get(int $id) : Items | null {
        return Items::where('id', $id)->get();
    }

    public static function getByIdAndUser(int $id, string $user){
        return Categories::where(['id'=>$id, 'user_id'=> $user]);
    }
}