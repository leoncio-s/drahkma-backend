<?php

namespace App\BankAccounts;

use App\Database\MySqlDatabaseImpl;
use App\Interfaces\Model;
use App\Interfaces\RepositoryInterface;
use App\BankAccounts\BankAccounts;
use Exception;
use PDOException;

class BankAccountsRepository implements RepositoryInterface{

    private $db;

    public function __construct(MySqlDatabaseImpl $db)
    {
        $this->db = $db;
    }

    public function save(array $data): array | null |Model
    {
        $account = null;

        try{
            $sqlSelect = "SELECT * FROM bank_accounts WHERE user=:user and agency=:agency and bankCode=:bankCode and accountNumber=:accountNumber limit 1;";
            $select = $this->db->select($sqlSelect,            
            [
                "user" => $data['user'],
                "agency" => $data["agency"],
                "bankCode" => $data["bankCode"],
                "accountNumber" => $data["accountNumber"]
            ]);

            if(count($select) > 0){
                $account = new BankAccounts();
                $account->toObject($select[0]);
            }else{
                $sql = "INSERT INTO bank_accounts(bankCode, user, bankName, agency, accountNumber) values(:bankCode, :user, :bankName, :agency, :accountNumber);";
                $prepare = $this->db->insert($sql, $data);
            
                $dat = $this->db->select("SELECT * FROM bank_accounts WHERE id=?", [$prepare]);

                if(count($dat) > 0){
                    $account = new BankAccounts();
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
            $sql = "Select * from bank_accounts where id=?;";
            $data = $this->db->select($sql, [$id]);
            if(count($data)>0){
                $account = new BankAccounts();
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
            $sql = "select * from bank_accounts where user=? order by bankName;";
            $data = $this->db->select($sql, [$userId]);

            if(count($data) > 0){
                foreach($data as $ac){
                    $account = new BankAccounts();
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
}