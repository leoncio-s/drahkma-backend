<?php

namespace App\Items;

use App\BankAccounts\BankAccountsRepository;
use App\Cards\CardsRepository;
use App\Categories\CategoriesRepository;
use App\Database\MySqlDatabaseImpl;
use App\Interfaces\Model;
use App\Interfaces\RepositoryInterface;
use App\Items\Items;
use App\Logging\Log;
use App\Logging\LogTypeEnum;
use App\TransfersBank\TransferBankRepository;
use Exception;
use PDOException;

class ItemsRepository implements RepositoryInterface{

    private $db;
    private CategoriesRepository $catRepo;
    private CardsRepository $cardRepo;
    private TransferBankRepository $trfBRepo;

    public function __construct(MySqlDatabaseImpl $db)
    {
        $this->db = $db;
        $this->catRepo = new CategoriesRepository($db);
        $this->cardRepo = new CardsRepository($db);
        $this->trfBRepo = new TransferBankRepository($db);
    }

    public function save(array $data): array | null |Model
    {
        $transfer_bank = null;
        $item = null;
        $fields = [
            'user' => $data['user'],
            'description'   => $data['description'],
            'expense'   => (int) $data['expense'],
            "value"     => $data['value'], 
            "date"      => $data['date'],
            "category"  => $data['category'],
            "card"      => $data['card'],
            "transfer_bank" => null
        ];

        try{
            if($data['transfer_bank'] != null){
                $data['transfer_bank']['user'] = $data['user'];
                $transfer_bank= $this->trfBRepo->save($data["transfer_bank"]);

                $fields['transfer_bank'] = $transfer_bank->getId();
            }
            if(isset($data['card'])){
                $card = $this->cardRepo->getByIdAndUser($data['card'], $data['user']);
                if($card == null) throw new Exception("invalid value for card field", 401);
            }

            if(isset($data['category'])){
                $cat = $this->catRepo->getByIdAndUser($data['category'], $data['user']);
                if($cat == null) throw new Exception("invalid value for category field", 401);
            }

            $sql = "INSERT INTO items(user, description, expense, value, date, category, card, transfer_bank) values(:user, :description, :expense, :value, :date, :category, :card, :transfer_bank);";

            $prepare = $this->db->insert($sql, $fields);
        
            if(is_numeric($prepare)){
                return $this->get((int)$prepare);
            }
            
        }catch(PDOException $e){
            // throw $e;
            new Log($e, LogTypeEnum::ERROR);
            return ['error' => $e->getCode()];
        }catch(Exception $e){
            new Log($e, LogTypeEnum::ERROR);
            return ['error' => $e->getCode()];
        }

        return $item;
    }

    public function update(array $data): ?Model
    {
        $transfer_bank = null;
        $item = null;
        $fields = [
            'id' => $data['id'],
            'user' => $data['user'],
            'description'   => $data['description'],
            "value"     => $data['value'], 
            "date"      => $data['date'],
            "category"  => $data['category'],
            "card"      => $data['card'],
            "transfer_bank" => null
        ];

        try{
            if($data['transfer_bank'] != null){
                $data['transfer_bank']['user'] = $data['user'];
                $transfer_bank= $this->trfBRepo->update($data["transfer_bank"]);

                $fields['transfer_bank'] = $transfer_bank->getId();
            }
            if(isset($data['card'])){
                $card = $this->cardRepo->getByIdAndUser($data['card'], $data['user']);
                if($card == null) throw new Exception("invalid value for card field", 401);
            }

            if(isset($data['category'])){
                $cat = $this->catRepo->getByIdAndUser($data['category'], $data['user']);
                if($cat == null) throw new Exception("invalid value for category field", 401);
            }

            $dbConn = $this->db->getDBConn();
            $dbConn->beginTransaction();
            try{
                $sql = "UPDATE items SET description=:description, value=:value, date=:date, category=:category, card=:card WHERE user=:user and id=:id;";
                $prepare = $dbConn->prepare($sql);
                $prepare->execute($fields);

                $dbConn->commit();

                if($prepare->rowCount() > 0){
                    return $this->getByIdAndUser($data['id'], $data['user']);
                }else{
                    return null;
                }

            }catch(PDOException $e){
                $dbConn->rollBack();
                throw $e;
            }
            
            return null;
        }catch(PDOException $e){
            // throw $e;
            new Log($e, LogTypeEnum::ERROR);
            return ['error' => $e->getCode()];
        }catch(Exception $e){
            new Log($e, LogTypeEnum::ERROR);
            return ['error' => $e->getCode()];
        }

        return $item;
    }

    public function delete($data): bool
    {
        return false;
    }

    public function get(int $id): ?Model
    {
        try{
            $sql = "select i.*, JSON_OBJECT('id', c.id, 'description', c.description) as category,
CASE WHEN i.card is not null then
	json_object('id', c2.id, 'type', c2.`type`, 'brand', c2.brand, 'expires_at', c2.expires_at, 'flag', c2.flag, 'last_4_digits', c2.last_4_digits, 'invoice_day', c2.invoice_day)
else
	i.card 
end as card,
case when i.transfer_bank is not null then
	json_object('id', t.id,'description', t.description, 'type', t.`type`,'bank_account',
	JSON_OBJECT('id', b.id, 'bankName', b.bankName, 'agency', b.agency, 'accountNumber', b.accountNumber, 'accountNumber', b.accountNumber, 'bankCode', b.bankCode))
else
	i.transfer_bank 
end as transfer_bank
from items i
left join categories c on c.id = i.category and c.`user` = i.`user` 
left join cards c2 on c2.id = i.card and c2.`user` = i.`user` 
left join transfer_bank t on t.id = i.transfer_bank
left join bank_accounts b on b.id=t.bank_account
where i.id=?";
            $data = $this->db->select($sql, [$id]);
            
            if(count($data)>0){
                $item = new Items();
                $item->toObject($data[0]);
                return $item;
            }

            return null;
        }catch(PDOException $e){
            return null;
        }
    }

    public function getByIdAndUser(int $id, int $userId): null | array | Items
    {
        try{
            $sql = "select i.*, JSON_OBJECT('id', c.id, 'description', c.description) as category,
CASE WHEN i.card is not null then
	json_object('id', c2.id, 'type', c2.`type`, 'brand', c2.brand, 'expires_at', c2.expires_at, 'flag', c2.flag, 'last_4_digits', c2.last_4_digits, 'invoice_day', c2.invoice_day)
else
	i.card 
end as card,
case when i.transfer_bank is not null then
	json_object('id', t.id,'description', t.description, 'type', t.`type`,'bank_account',
	JSON_OBJECT('id', b.id, 'bankName', b.bankName, 'agency', b.agency, 'accountNumber', b.accountNumber, 'accountNumber', b.accountNumber, 'bankCode', b.bankCode))
else
	i.transfer_bank 
end as transfer_bank
from items i
left join categories c on c.id = i.category and c.`user` = i.`user` 
left join cards c2 on c2.id = i.card and c2.`user` = i.`user` 
left join transfer_bank t on t.id = i.transfer_bank
left join bank_accounts b on b.id=t.bank_account
where i.id=? and i.user=?";
            $data = $this->db->select($sql, [$id, $userId]);
            
            if(count($data)>0){
                $item = new Items();
                $item->toObject($data[0]);
                return $item;
            }

            return null;
        }catch(PDOException $e){
            new Log($e);
            return ["errors" => $e->getCode()];
        }
    }

    public function getByUser(int $userId){
        try{
            $ret = [];
            $sql = "select i.*, JSON_OBJECT('id', c.id, 'description', c.description) as category,
CASE WHEN i.card is not null then
	json_object('id', c2.id, 'type', c2.`type`, 'brand', c2.brand, 'expires_at', c2.expires_at, 'flag', c2.flag, 'last_4_digits', c2.last_4_digits, 'invoice_day', c2.invoice_day)
else
	i.card 
end as card,
case when i.transfer_bank is not null then
	json_object('id', t.id,'description', t.description, 'type', t.`type`,'bank_account',
	JSON_OBJECT('id', b.id, 'bankName', b.bankName, 'agency', b.agency, 'accountNumber', b.accountNumber, 'accountNumber', b.accountNumber, 'bankCode', b.bankCode))
else
	i.transfer_bank 
end as transfer_bank
from items i
left join categories c on c.id = i.category and c.`user` = i.`user` 
left join cards c2 on c2.id = i.card and c2.`user` = i.`user` 
left join transfer_bank t on t.id = i.transfer_bank
left join bank_accounts b on b.id=t.bank_account
where i.user=? order by date desc;";
            $data = $this->db->select($sql, [$userId]);

            if(count($data) > 0){
                foreach($data as $ac){
                    $account = new Items();
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

    public function getInflow(string $start_date, string $finish_date, int $userId):array{
        try{
            $sql = "select i.*, JSON_OBJECT('id', c.id, 'description', c.description) as category,
CASE WHEN i.card is not null then
	json_object('id', c2.id, 'type', c2.`type`, 'brand', c2.brand, 'expires_at', c2.expires_at, 'flag', c2.flag, 'last_4_digits', c2.last_4_digits, 'invoice_day', c2.invoice_day)
else
	i.card 
end as card,
case when i.transfer_bank is not null then
	json_object('id', t.id,'description', t.description, 'type', t.`type`,'bank_account',
	JSON_OBJECT('id', b.id, 'bankName', b.bankName, 'agency', b.agency, 'accountNumber', b.accountNumber, 'accountNumber', b.accountNumber, 'bankCode', b.bankCode))
else
	i.transfer_bank 
end as transfer_bank
from items i
left join categories c on c.id = i.category and c.`user` = i.`user` 
left join cards c2 on c2.id = i.card and c2.`user` = i.`user` 
left join transfer_bank t on t.id = i.transfer_bank
left join bank_accounts b on b.id=t.bank_account
where i.user=:user_id
and i.date between :start_date and :finish_date
and i.expense=false
order by date desc;";
            $prep = $this->db->select($sql,[
                "start_date" => $start_date,
                "finish_date" => $finish_date,
                "user_id" => $userId
            ]);
            $toRet = [];
            if(count($prep, COUNT_RECURSIVE) > 0 || $prep != null){
                foreach($prep as $data){
                    $item = new Items();
                    $item->toObject($data);
                    array_push($toRet, $item->toArray());
                }
            }
            
            return $toRet;
            
        }catch(PDOException $e){
            return [];
        }catch(Exception $e){
            return [];
        }
    }

    public function getOutflow(string $start_date, string $finish_date, int $userId){
        try{
            $sql = "select i.*, JSON_OBJECT('id', c.id, 'description', c.description) as category,
CASE WHEN i.card is not null then
	json_object('id', c2.id, 'type', c2.`type`, 'brand', c2.brand, 'expires_at', c2.expires_at, 'flag', c2.flag, 'last_4_digits', c2.last_4_digits, 'invoice_day', c2.invoice_day)
else
	i.card 
end as card,
case when i.transfer_bank is not null then
	json_object('id', t.id,'description', t.description, 'type', t.`type`,'bank_account',
	JSON_OBJECT('id', b.id, 'bankName', b.bankName, 'agency', b.agency, 'accountNumber', b.accountNumber, 'accountNumber', b.accountNumber, 'bankCode', b.bankCode))
else
	i.transfer_bank 
end as transfer_bank
from items i
left join categories c on c.id = i.category and c.`user` = i.`user` 
left join cards c2 on c2.id = i.card and c2.`user` = i.`user` 
left join transfer_bank t on t.id = i.transfer_bank
left join bank_accounts b on b.id=t.bank_account
where i.user=:user_id
and i.date between :start_date and :finish_date
and i.expense=true
order by date desc;";
            $prep = $this->db->select($sql,[
                "start_date" => $start_date,
                "finish_date" => $finish_date,
                "user_id" => $userId
            ]);
            $toRet = [];
            if(count($prep, COUNT_RECURSIVE) > 0 || $prep != null){
                foreach($prep as $data){
                    $item = new Items();
                    array_push($toRet, $item->toObject($data));
                }
            }

            return $toRet;
            
        }catch(PDOException $e){
            return null;
        }catch(Exception $e){
            return null;
        }
    }

    public function getAmounts(string $start_date, string $finish_date, int $userId){
        try{
            $sql = "SELECT sum(value), 'outflow' as type FROM items where date between :start_date and :finish_date and user=:user_id and expense=true
            union all
            SELECT sum(value), 'inflow' as type FROM items where date between :start_date and :finish_date and user=:user_id and expense=false
            union all
            select in.sum - out.sum, 'amount' as type from (SELECT sum(value), 'outflow' as type FROM items where date between :start_date and :finish_date and user=:user_id and expense=true) out,
            (SELECT sum(value), 'inflow' as type FROM items where date between :start_date and :finish_date and user=:user_id and expense=false) in";

            $prep = $this->db->select($sql,[
                "start_date" => $start_date,
                "finish_date" => $finish_date,
                "user_id" => $userId
            ]);

            return $prep;
            
        }catch(PDOException $e){
            return null;
        }catch(Exception $e){
            return null;
        }
    }


}