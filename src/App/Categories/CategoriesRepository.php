<?php

namespace App\Categories;

use App\Database\MySqlDatabaseImpl;
use App\Interfaces\Model;
use App\Interfaces\RepositoryInterface;
use App\Categories\Categories;
use Exception;
use PDOException;

class CategoriesRepository implements RepositoryInterface
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
            $sqlSelect = "SELECT * FROM categories WHERE user=:user and upper(description)=upper(:description) limit 1;";
            $select = $this->db->select(
                $sqlSelect,
                [
                    "user" => $data['user'],
                    "description" => $data["description"]
                ]
            );

            if (count($select) > 0) {
                $account = new Categories();
                $account->toObject($select[0]);
            } else {
                $sql = "INSERT INTO categories(user, description) values(:user, upper(:description));";
                $prepare = $this->db->insert($sql, [
                    "user" => $data['user'],
                    "description" => $data["description"]
                ]);

                $dat = $this->db->select("SELECT * FROM categories WHERE id=?", [$prepare]);

                if (count($dat) > 0) {
                    $account = new Categories();
                    $account->toObject($dat[0]);
                }
            }
        } catch (PDOException $e) {
            throw $e;
            return ['error' => $e->getCode()];
        } catch (Exception $e) {
            return ['error' => $e->getCode()];
        }

        return $account;
    }

    public function update(array $data) : Model | array | null
    {


        try {
            $sqlSelect = "SELECT * FROM categories WHERE user=:user and upper(description)=upper(:description)  and id<>:id limit 1;";
            $select = $this->db->select(
                $sqlSelect,
                [
                    "user" => $data['user'],
                    "description" => $data["description"],
                    "id" => $data['id']
                ]
            );

            if (count($select) > 0) {
                return ['error' => "You already have a category with this description"];
            } else {
                $conn = $this->db->getDBConn();
                $conn->beginTransaction();
                try {
                    $sql = "UPDATE categories SET description=:description WHERE user=:user and id=:id;";
                    $prepare = $conn->prepare($sql);

                    $prepare->execute([
                        "user" => $data['user'],
                        "id" => $data['id'],
                        "description" => $data["description"]
                    ]);

                    $conn->commit();

                    $dat = $this->db->select("SELECT * FROM categories WHERE id=?", [$data['id']]);

                    if (count($dat) > 0) {
                        $categories = new Categories();
                        $categories->toObject($dat[0]);
                        return $categories;
                    } else {
                        return ['error' => "Não foi possível processar a solicitação"];
                    }
                } catch (PDOException $e) {
                    $conn->rollBack();
                    return ['error' => $e->getCode()];
                } catch (Exception $e) {
                    $conn->rollBack();
                    return ['error' => $e->getCode()];
                }
            }
        } catch (PDOException $e) {
            throw $e;
            return ['error' => $e->getCode()];
        } catch (Exception $e) {
            return ['error' => $e->getCode()];
        }

        return $account;
    }

    public function delete($data) : bool | array
    {

        $it = "SELECT count(*) as count FROM items where category=:id and user=:user";
        $select = $this->db->select($it, ['id' => $data['id'], "user" => $data['user']]);

        if ($select[0]['count'] > 0) {
            return ['error' => "Have movement linked to this category, cannot delete"];
        }


        $conn = $this->db->getDBConn();
        $conn->beginTransaction();
        try {
            $sql = "DELETE FROM categories WHERE id=:id and user=:user";

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
            return ['error' => $e->getMessage()];
        } catch (Exception $e) {
            $conn->rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function get(int $id) : ?Model
    {
        try {
            $sql = "Select * from categories where id=?;";
            $data = $this->db->select($sql, [$id]);
            if (count($data) > 0) {
                $account = new Categories();
                $account->toObject($data[0]);
                return $account;
            }

            return null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getByUser(int $userId)
    {
        try {
            $ret = [];
            $sql = "select * from categories where user=? order by description;";
            $data = $this->db->select($sql, [$userId]);

            if (count($data) > 0) {
                foreach ($data as $ac) {
                    $account = new Categories();
                    $account->toObject($ac);
                    array_push($ret, $account->toArray());
                    // unset($account);
                }
            }
            return $ret;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getByIdAndUser(int $id, int $userId)
    {
        try {
            $ret = null;
            $sql = "select * from categories where user=? and id=? order by description;";
            $data = $this->db->select($sql, [$userId, $id]);

            if (count($data) > 0) {
                // foreach($data as $ac){
                $ret = new Categories();
                $ret->toObject($data[0]);
                // array_push($ret, $account->toArray());
                // unset($account);
            }
            return $ret;
        } catch (PDOException $e) {
            return null;
        }
    }
}
