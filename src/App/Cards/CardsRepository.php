<?php

namespace App\Cards;

use App\Database\MySqlDatabaseImpl;
use App\Interfaces\Model;
use App\Interfaces\RepositoryInterface;
use App\Cards\Cards;
use Exception;
use PDOException;

class CardsRepository implements RepositoryInterface{

    private $db;

    public function __construct(MySqlDatabaseImpl $db)
    {
        $this->db = $db;
    }

    public function save(array $data): array | null |Model
    {
        $account = null;

        try{
            $sqlSelect = "SELECT * FROM cards WHERE user=:user and last_4_digits=:last_4_digits and flag=:flag and type=:type limit 1;";
            $select = $this->db->select($sqlSelect,            
            [
                "user" => $data['user'],
                "type" => $data["type"],
                "last_4_digits" => $data["last_4_digits"],
                "flag" => $data["flag"]
            ]);

            if(count($select) > 0){
                $account = new Cards();
                $account->toObject($select[0]);
            }else{
                $sql = "INSERT INTO cards(type, user, brand, flag, expires_at, last_4_digits, invoice_day)
                    values(:type, :user, :brand, :flag, :expires_at, :last_4_digits, :invoice_day);";

                $fields = [
                    "user" => $data['user'],
                    "type" => $data['type'],
                    "brand" => $data['brand'],
                    "flag" => $data['flag'], 
                    "expires_at" => $data['expires_at'],
                    "last_4_digits" => $data['last_4_digits'],
                    "invoice_day" => $data['invoice_day']
                ];

                $prepare = $this->db->insert($sql, $fields);
            
                $dat = $this->db->select("SELECT * FROM cards WHERE id=?", [$prepare]);

                if(count($dat) > 0){
                    $account = new Cards();
                    $account->toObject($dat[0]);
                }
            }
            
        }catch(PDOException $e){
            throw $e;
            return ['error' => $e->getCode()];
        }catch(Exception $e){
            throw $e;
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
            $sql = "Select * from bank_accounts where id=?;";
            $data = $this->db->select($sql, [$id]);
            if(count($data)>0){
                $account = new Cards();
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
            $sql = "select * from cards where user=? order by brand;";
            $data = $this->db->select($sql, [$userId]);

            if(count($data) > 0){
                foreach($data as $ac){
                    $account = new Cards();
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

    public function getByIdAndUser(int $id, int $userId): ?Model
    {
        try{
            $sql = "Select * from bank_accounts where id=? and user=?;";
            $data = $this->db->select($sql, [$id, $userId]);
            if(count($data)>0){
                $account = new Cards();
                $account->toObject($data[0]);
                return $account;
            }

            return null;
        }catch(PDOException $e){
            return null;
        }
    }
}