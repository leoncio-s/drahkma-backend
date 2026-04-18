<?php

namespace App\Cards;

use App\Database\MySqlDatabaseImpl;
use App\Interfaces\Model;
use App\Interfaces\RepositoryInterface;
use App\Cards\Cards;
use App\Logging\Log;
use App\Logging\LogTypeEnum;
use Exception;
use PDOException;

class CardsRepository implements RepositoryInterface
{

    private $db;

    public function __construct(MySqlDatabaseImpl $db)
    {
        $this->db = $db;
    }

    public function save(array $data) : array | null |Model
    {
        $account = null;

        try {
            $sqlSelect = "SELECT * FROM cards WHERE user=:user and last_4_digits=:last_4_digits and flag=:flag and type=:type limit 1;";
            $select = $this->db->select(
                $sqlSelect,
                [
                    "user" => $data['user'],
                    "type" => $data["type"],
                    "last_4_digits" => $data["last_4_digits"],
                    "flag" => $data["flag"]
                ]
            );

            if (count($select) > 0) {
                $account = new Cards();
                $account->toObject($select[0]);
            } else {
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

                if (count($dat) > 0) {
                    $account = new Cards();
                    $account->toObject($dat[0]);
                }
            }
        } catch (PDOException $e) {
            new Log($e, LogTypeEnum::ERROR);
            return ['error' => $e->getCode()];
        } catch (Exception $e) {
            new Log($e, LogTypeEnum::ERROR);
            return ['error' => $e->getCode()];
        }

        return $account;
    }

    public function update(array $data) : array | null |Model
    {
        $account = null;

        try {
            $sqlSelect = "SELECT * FROM cards WHERE user=:user and last_4_digits=:last_4_digits and flag=:flag and type=:type and id<>:id limit 1;";
            $select = $this->db->select(
                $sqlSelect,
                [
                    "user" => $data['user'],
                    "type" => $data["type"],
                    "last_4_digits" => $data["last_4_digits"],
                    "flag" => $data["flag"],
                    "id" => $data['id']
                ]
            );

            if (count($select) > 0) {
                $account = new Cards();
                $account->toObject($select[0]);
                return ["errors" => "Já existe um cartão com esses dados", "data" => $account];
            } else {
                $sql = "UPDATE cards
                    SET type=:type, 
                        brand=:brand,
                        flag=:flag,
                        expires_at=:expires_at,
                        last_4_digits=:last_4_digits, invoice_day=:invoice_day
                    WHERE id=:id and user=:user;";

                $fields = [
                    "user" => $data['user'],
                    "type" => $data['type'],
                    "brand" => $data['brand'],
                    "flag" => $data['flag'],
                    "expires_at" => $data['expires_at'],
                    "last_4_digits" => $data['last_4_digits'],
                    "invoice_day" => $data['invoice_day'],
                    "id" => $data['id']
                ];

                new Log($data, LogTypeEnum::ERROR);

                $dbConn = $this->db->getDBConn();
                $dbConn->beginTransaction();
                try {
                    $prepare = $dbConn->prepare($sql);
                    $prepare->execute($fields);

                    // if($prepare->rowCount() >= 1){

                    // }
                    $dbConn->commit();
                    $dat = $this->db->select("SELECT * FROM cards WHERE id=?", [$data['id']]);

                    if (count($dat) > 0) {
                        $account = new Cards();
                        $account->toObject($dat[0]);
                        return $account;
                    }
                } catch (Exception $e) {
                    throw $e;
                }
            }
        } catch (PDOException $e) {
            // throw $e;
            new Log($e, LogTypeEnum::ERROR);
            return ['errors' => $e->getCode()];
        } catch (Exception $e) {
            // throw $e
            new Log($e, LogTypeEnum::ERROR);
            return ['errors' => $e->getCode()];
        }

        return $account;
    }

    public function delete($data) : bool | array
    {

        $it = "SELECT count(*) as count FROM items where card=:id and user=:user";
        $select = $this->db->select($it, ['id' => $data['id'], "user" => $data['user']]);

        if ($select[0]['count'] > 0) {
            return ['errors' => "Have movement linked to this bank account, cannot delete"];
        }


        $conn = $this->db->getDBConn();
        $conn->beginTransaction();
        try {
            $sql = "DELETE FROM cards WHERE id=:id and user=:user";

            $prep = $conn->prepare($sql);

            $prep->execute([
                "id" => $data['id'],
                "user" => $data['user']
            ]);

            if ($prep->rowCount() > 0) {
                $conn->commit();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            $conn->rollBack();
            new Log($e, LogTypeEnum::ERROR);
            return ['errors' => $e->getMessage()];
        } catch (Exception $e) {
            $conn->rollBack();
            new Log($e, LogTypeEnum::ERROR);
            return ['errors' => $e->getMessage()];
        }
    }

    public function get(int $id) : ?Model
    {
        try {
            $sql = "Select * from bank_accounts where id=?;";
            $data = $this->db->select($sql, [$id]);
            if (count($data) > 0) {
                $account = new Cards();
                $account->toObject($data[0]);
                return $account;
            }

            return null;
        } catch (PDOException $e) {
            new Log($e, LogTypeEnum::ERROR);
            return null;
        }
    }

    public function getByUser(int $userId):?array
    {
        try {
            $ret = [];
            $sql = "select * from cards where user=? order by brand;";
            $data = $this->db->select($sql, [$userId]);

            if (count($data) > 0) {
                foreach ($data as $ac) {
                    $account = new Cards();
                    $account->toObject($ac);
                    array_push($ret, $account->toArray());
                    // unset($account);
                }
            }
            return $ret;
        } catch (PDOException $e) {
            new Log($e, LogTypeEnum::ERROR);
            return null;
        }
    }

    public function getByIdAndUser(int $id, int $userId) : ?Model
    {
        try {
            $sql = "Select * from cards where id=? and user=?;";
            $data = $this->db->select($sql, [$id, $userId]);

            if (count($data) > 0) {
                $account = new Cards();
                $account->toObject($data[0]);
                return $account;
            }

            return null;
        } catch (PDOException $e) {
            new Log($e, LogTypeEnum::ERROR);
            return null;
        }
    }
}
