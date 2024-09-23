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
                $prepare = $this->db->insert($sql, [
                    "user" => $data['user'],
                    "agency" => $data["agency"],
                    "bankCode" => $data["bankCode"],
                    "accountNumber" => $data["accountNumber"],
                    "bankName" => $data["bankName"]
                ]);
            
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

    public function update(array $data): Model | array | null
    {

        
        try{
            $sqlSelect = "SELECT * FROM bank_accounts WHERE user=:user and upper(bankName)=upper(:bankName) and agency=:agency and accountNumber=:accountNumber and bankCode=:bankCode and id<>:id limit 1;";
            $select = $this->db->select($sqlSelect,            
            [
                "user" => $data['user'],
                "bankName" => $data["bankName"],
                "id" => $data['id'],
                "bankCode" => $data['bankCode'],
                "agency" => $data['agency'],
                "accountNumber"  => $data['accountNumber'],
            ]);

            if(count($select) > 0){
                return ['error' => "You already have a bank account with this data"];
            }else{
                $conn = $this->db->getDBConn();
                $conn->beginTransaction();
                try{
                    $sql = "UPDATE bank_accounts SET bankCode=:bankCode, bankName=:bankName, agency=:agency, accountNumber=:accountNumber  WHERE user=:user and id=:id;";
                    $prepare = $conn->prepare($sql);

                    $prepare->execute( [
                        "user" => $data['user'],
                        "bankName" => $data["bankName"],
                        "id" => $data['id'],
                        "bankCode" => $data['bankCode'],
                        "agency" => $data['agency'],
                        "accountNumber"  => $data['accountNumber'],
                    ]);

                    $conn->commit();
                
                    $dat = $this->db->select("SELECT * FROM bank_accounts WHERE id=?", [$data['id']]);

                    if(count($dat) > 0){
                        $categories = new BankAccounts();
                        $categories->toObject($dat[0]);
                        return $categories;
                    }else{
                        return ['error'=> "Não foi possível processar a solicitação"];
                    }
                }catch(PDOException $e){
                    $conn->rollBack();
                    return ['error' => $e->getCode()];
                }catch(Exception $e){
                    $conn->rollBack();
                    return ['error' => $e->getCode()];
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

    public function delete($data): bool | array
    {

        $it = "SELECT count(*) as count FROM transfer_bank where bank_account=:id and user=:user";
        $select = $this->db->select($it, ['id'=>$data['id'], "user"=>$data['user']]);

        if($select[0]['count'] > 0){
            return ['error' => "Have movement linked to this bank account, cannot delete"];
        }


        $conn = $this->db->getDBConn();
        $conn->beginTransaction();
        try{
            $sql = "DELETE FROM bank_accounts WHERE id=:id and user=:user";

            $prep = $conn->prepare($sql);

            $prep->execute([
                "id" => $data['id'],
                "user" => $data['user']
            ]);

            if($prep->rowCount() > 0){
                $conn->commit();
                return true;
            }
            return false;

        }catch(PDOException $e){
            $conn->rollBack();
            return ['error' => $e->getMessage()];
        }catch(Exception $e){
            $conn->rollBack();
            return ['error' => $e->getMessage()];
        }
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
                }
            }
            return $ret;
        }catch(PDOException $e){
            return null;
        }
    }

    public function getByIdAndUser(int $id, int $userId){
        try{
            $ret = [];
            $sql = "select * from bank_accounts where user=? and id=? order by bankName;";
            $data = $this->db->select($sql, [$userId, $id]);

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