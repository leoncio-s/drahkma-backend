<?php

namespace App\Users;

use App\Database\MySqlDatabaseImpl;
use App\Logging\Log;
use App\Users\User;
use DateTime;
use Exception;
use PDO;
use PDOException;

use function PHPSTORM_META\type;

class UserRepository implements UserRepositoryInterface
{

    private $db;

    public function __construct(MySqlDatabaseImpl $db)
    {
        $this->db = $db;
    }

    public function get(int $id): User | null
    {
        $sql = "SELECT * FROM users WHERE fullname like '%a%' or id=:id";
        $query = $this->db->select($sql, ['id' => $id]);

        if (count($query) != 0) {
            $user = new User();
            $user = $user->toObject($query[0]);
            return $user;
        }

        return null;
    }

    public function save(array $data): User | array | null
    {
        $values = [
            "fullname" => $data['fullname'],
            "email" => $data['email'],
            "phone_number" => $data['phone_number'],
            "password" => $data['password']
        ];

        $sql = "INSERT INTO users(fullname, email, phone_number, password) values(:fullname, :email, :phone_number, :password);";

        try {
            $ret = $this->db->insert($sql, $values);

            // echo($ret);

            $query = $this->db->select("SELECT * FROM users WHERE id=:id", ["id" => $ret]);


            $user = new User();
            $user->toObject($query[0]);

            // var_dump($user->getId());
            return $user;
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                return ['error' => "Duplicate value for e-mail field"];
            } else {
                return ['error' => $e->getCode()];
            }
        }

        return null;
    }

    public function update(array $data): User
    {
        // $validated = UserValidator::make($data)->validated();
        $user = self::get($data['id']);

        if ($user == null || isset($user)) {
            throw new Exception("User not found");
        }

        // $user->name = $validated['name'];
        // $user->email = $validated['email'];
        // $user->phone_number = $validated['phone_number'];
        // $user->password = $validated['password'];

        // $user->refresh();

        if ($user->getEmail() != $data['email']) {
            $data['email_verified_at'] = null;
        }

        $data['updated_at'] = new DateTime();

        $sql = "update users set fullname=:fullname, phone_number=:phone_number, email=:email, actived=:actived, email_verified_at=:email_verified_at where id=:id;";

        $ret = $this->db->select($sql, $data);

        $nUser = new User();
        $nUser->toObject($ret);
        return $nUser;
    }

    public function delete(int $id): int | bool | null
    {
        $sql = "delete from users where id=?;";

        $ret = $this->db->select($sql, [$id]);
        return (bool) $ret;
    }

    public function getByEmail(string $email): User | null | array
    {
        try{
            $sql = "SELECT * FROM users where email=? limit 1;";
        
            $ret = $this->db->select($sql, [$email]);
    
            if (count($ret) == 0) return null;
            $user = new User();
    
            $user->toObject($ret[0]);
    
            return $user;
        }catch(PDOException $e){
            new Log($e);
            return ['errors'=>$e];
        }

    }

    public function updatePassword(int $id, string $password): User
    {
        $sql = "UPDATE USERS SET password=?, updated_at=now() where id=?;";
        $uAt = self::get($id);
        $uAt->setPassword($password);
        $uAt->setUpdatedAt((string)(new DateTime()));

        $ret = $this->db->select($sql, [$uAt->getPassword(), $uAt->getId()]);

        $uAt->toObject($ret);

        return $uAt;
    }

    public function generateEmailVerification($email, $token, $exp_At)
    {
        $db = $this->db->getDBConn();
        $db->beginTransaction();
        try {
            $query  = $db->prepare('INSERT INTO email_verified(email, token, expires_at) values(?, ?, ?);');
            $query->execute([$email, $token, $exp_At]);

            $db->commit();
            return true;
        } catch (PDOException $e) {
            $db->rollBack();
            throw $e;
        }

        return false;
    }

    public function getEmailByVerifyToken(string $token)
    {
        $conn = $this->db->getDBConn();
        try {
            $ret = $conn->prepare("SELECT email, expires_at FROM email_verified where token=?");
            $ret->execute([$token]);

            $ret = $ret->fetchAll();

            if (count($ret) == 1) {
                $exp = new DateTime($ret[0]['expires_at']);
                $at = (new DateTime('now', $exp->getTimezone()))->getTimestamp();

                $q = $conn->prepare("DELETE FROM email_verified WHERE token=?;");
                $q->execute([$token]);
                
                if (($exp->getTimestamp() - $at) <= 0) {
                    return false;
                } else {
                    $q = $conn->prepare("UPDATE users SET email_verified_at=CURRENT_TIMESTAMP, actived=1 WHERE email=?");
                    $q->execute([$ret[0]['email']]);
                    return true;
                }
            } else {
                return false;
            }
        } catch (PDOException $e) {
            // echo $e;
            return false;
        }
    }
}
