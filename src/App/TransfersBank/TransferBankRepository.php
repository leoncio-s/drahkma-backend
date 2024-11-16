<?php

namespace App\TransfersBank;

use App\BankAccounts\BankAccountsRepository;
use App\Database\MySqlDatabaseImpl;
use App\Interfaces\Model;
use App\Interfaces\RepositoryInterface;
use App\Logging\Log;
use App\Logging\LogTypeEnum;
use App\TransfersBank\TransferBank;
use Exception;
use PDOException;

class TransferBankRepository implements RepositoryInterface
{

    private $db;

    private BankAccountsRepository $bnkRepo;

    public function __construct(MySqlDatabaseImpl $db)
    {
        $this->db = $db;
        $this->bnkRepo = new BankAccountsRepository($db);
    }

    public function save(array $data): array | null |TransferBank
    {
        $account = null;

        try {
            $fields = [
                "user" => $data['user'],
                "type" => $data['type'],
                "bank_account" => $data["bank_account"],
                "description" => $data["description"]
            ];
            $bnkAccount = $this->bnkRepo->getByIdAndUser($data['bank_account'], $data['user']);

            if ($bnkAccount == null) throw new Exception("Invalid bank_account", 403);

            $sql = "INSERT INTO transfer_bank(user, type, bank_account, description) values(:user, :type, :bank_account, :description);";

            // var_dump($data);
            $prepare = $this->db->insert($sql, $fields);

            if (is_numeric($prepare)) return $this->get((int)$prepare);
        } catch (PDOException $e) {
            // throw $e;
            new Log($e, LogTypeEnum::ERROR);
            return ['error' => $e->getCode()];
        } catch (Exception $e) {
            new Log($e, LogTypeEnum::ERROR);
            return ['error' => $e->getCode()];
        }

        return $account;
    }

    public function update(array $data): array | null | TransferBank
    {
        $account = null;
        try {
            $fields = [
                "id" => $data['id'],
                "user" => $data['user'],
                "type" => $data['type'],
                "bank_account" => $data["bank_account"],
                "description" => $data["description"]
            ];

            $bnkAccount = $this->bnkRepo->getByIdAndUser($data['bank_account'], $data['user']);

            if ($bnkAccount == null) throw new Exception("Invalid bank_account", 403);

            $dbCon = $this->db->getDBConn();
            $dbCon->beginTransaction();
            try {

                $sql = "UPDATE transfer_bank SET type=:type, bank_account=:bank_account, description=:description WHERE id=:id and user=:user";
                $prepare = $dbCon->prepare($sql);
                $prepare->execute($fields);

                new Log("fectch item :" . $prepare->fetch());

                $dbCon->commit();

                return $this->getByIdAndUser($data['id'], $data['user']);
            } catch (Exception $e) {
                $dbCon->rollBack();
                throw $e;
            }
        } catch (PDOException $e) {
            new Log($e, LogTypeEnum::ERROR);
            return ['errors' => $e->getCode()];
        } catch (Exception $e) {
            new Log($e, LogTypeEnum::ERROR);
            return ['errors' => $e->getCode()];
        }

        return $account;
    }

    public function delete($id): array | bool
    {
        $dbCon = $this->db->getDBConn();
        $dbCon->beginTransaction();
        try {
            $sql = "DELETE FROM transfer_bank WHERE id=?";
            $prepare = $dbCon->prepare($sql);
            $prepare->execute([$id]);

            if ($prepare->rowCount() > 0) {
                $dbCon->commit();
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            new Log($e, LogTypeEnum::ERROR);
            return ['errors' => $e->getCode()];
        } catch (Exception $e) {
            new Log($e, LogTypeEnum::ERROR);
            return ['errors' => $e->getMessage()];
        }
    }

    public function get(int $id): ?TransferBank
    {
        try {
            $sql = "Select t.*, JSON_OBJECT('id', b.id, 'bankName', b.bankName, 'agency', b.agency, 'accountNumber', b.accountNumber, 'accountNumber', b.accountNumber, 'bankCode', b.bankCode) as bank_account from transfer_bank t inner join bank_accounts b on b.id=t.bank_account where t.id=?;";
            $data = $this->db->select($sql, [$id]);
            if (count($data) > 0) {
                $account = new TransferBank();
                $account->toObject($data[0]);
                return $account;
            }

            return null;
        } catch (PDOException $e) {
            new Log($e, LogTypeEnum::ERROR);
            throw $e;
        } catch (Exception $e) {
            new Log($e, LogTypeEnum::ERROR);
            throw $e;
        }
    }

    public function getByUser(int $userId):?array
    {
        try {
            $ret = [];
            $sql = "select * from transfer_bank where user=? order by description;";
            $data = $this->db->select($sql, [$userId]);

            if (count($data) > 0) {
                foreach ($data as $ac) {
                    $account = new TransferBank();
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

    public function getByIdAndUser(int $id, int $userId): array | null | TransferBank
    {
        try {
            $ret = null;
            $sql = "Select t.*, JSON_OBJECT('id', b.id, 'bankName', b.bankName, 'agency', b.agency, 'accountNumber', b.accountNumber, 'accountNumber', b.accountNumber, 'bankCode', b.bankCode) as bank_account from transfer_bank t inner join bank_accounts b on b.id=t.bank_account where t.user=? and t.id=? order by t.description;";
            $data = $this->db->select($sql, [$userId, $id]);

            if (count($data) > 0) {
                $ret = new TransferBank();
                $ret->toObject($data[0]);
            }
            return $ret;
        } catch (PDOException $e) {
            new Log($e, LogTypeEnum::ERROR);
            return ['errors' => $e->getCode()];
        }
    }
}
