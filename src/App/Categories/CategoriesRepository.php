<?php

namespace App\Categories;

use App\Database\MySqlDatabaseImpl;
use App\Interfaces\Model;
use App\Interfaces\RepositoryInterface;
use App\Categories\Categories;
use Exception;
use PDOException;

class CategoriesRepository implements RepositoryInterface{

    private $db;

    public function __construct(MySqlDatabaseImpl $db)
    {
        $this->db = $db;
    }

    public function save(array $data): array | null |Model
    {
        $account = null;

        try{
            $sqlSelect = "SELECT * FROM categories WHERE user=:user and upper(description)=upper(:description) limit 1;";
            $select = $this->db->select($sqlSelect,            
            [
                "user" => $data['user'],
                "description" => $data["description"]
            ]);

            if(count($select) > 0){
                $account = new Categories();
                $account->toObject($select[0]);
            }else{
                $sql = "INSERT INTO categories(user, description) values(:user, upper(:description));";
                $prepare = $this->db->insert($sql, $data);
            
                $dat = $this->db->select("SELECT * FROM categories WHERE id=?", [$prepare]);

                if(count($dat) > 0){
                    $account = new Categories();
                    $account->toObject($dat[0]);
                }
            }
            
        }catch(PDOException $e){
            throw $e;
            return ['error' => $e->getCode()];
        }catch(Exception $e){
            return ['error' => $e->getCode()];
        }

        return $account;
    }

    public function update(array $data): ?Model
    {
        return null;
    }

    public function delete($data): bool
    {
        return false;
    }

    public function get(int $id): ?Model
    {
        try{
            $sql = "Select * from categories where id=?;";
            $data = $this->db->select($sql, [$id]);
            if(count($data)>0){
                $account = new Categories();
                $account->toObject($data[0]);
                return $account;
            }

            return null;
        }catch(PDOException $e){
            return null;
        }
    }

    public function getByUser(int $userId){
        try{
            $ret = [];
            $sql = "select * from categories where user=? order by description;";
            $data = $this->db->select($sql, [$userId]);

            if(count($data) > 0){
                foreach($data as $ac){
                    $account = new Categories();
                    $account->toObject($ac);
                    array_push($ret, $account->toArray());
                    // unset($account);
                }
            }
            return $ret;
        }catch(PDOException $e){
            return null;
        }
    }

    public function getByIdAndUser(int $id, int $userId){
        try{
            $ret = null;
            $sql = "select * from categories where user=? and id=? order by description;";
            $data = $this->db->select($sql, [$userId, $id]);

            if(count($data) > 0){
                // foreach($data as $ac){
                $ret = new Categories();
                $ret->toObject($data[0]);
                    // array_push($ret, $account->toArray());
                    // unset($account);
                }
            return $ret;
        }catch(PDOException $e){
            return null;
        }
    }
}