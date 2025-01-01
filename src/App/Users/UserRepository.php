<?php

namespace App\Users;

use App\Database\Databases;
use App\Interfaces\RepositoryInterface;
use App\Logging\Log;
use App\Logging\LogTypeEnum;
use App\Users\User;
use App\Utils\Http\HttpStatus;
use DateTime;
use Exception;
use PDOException;

class UserRepository implements RepositoryInterface
{

    private $db;

    public function __construct(Databases $db)
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
            new Log($e, LogTypeEnum::ERROR);
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
        $user = self::get($data['id']);

        if ($user == null || isset($user)) {
            throw new Exception("User not found");
        }

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
        $sql = "SELECT * FROM users where email=? limit 1;";
    
        $ret = $this->db->select($sql, [$email]);

        if (count($ret) == 0) return null;
        $user = new User();

        $user->toObject($ret[0]);

        return $user;

    }

    public function updatePassword(int $id, string $password): User
    {
        $sql = "UPDATE USERS SET password=?, updated_at=now() where id=?;";
        $uAt =  self::get($id);
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
            new Log($e, LogTypeEnum::ERROR);
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
            new Log($e, LogTypeEnum::ERROR);
            return false;
        }
    }

    public function generateForgetPasswordRequest(int $idUser, string $code)
    {
        $dbConn = $this->db->getDBConn();

        $dbConn->beginTransaction();
        try{
            $query = "insert into forget_password(user, code) values (?,?)";
            $prepare = $dbConn->prepare($query, [$idUser, $code]);
            $prepare->execute();
            if($prepare->rowCount() == 1){
                $dbConn->commit();
                return true;
            }else{
                return false;
            }
        }catch(Exception $ex){
            $dbConn->rollBack();
            throw $ex;
        }
    }

    public function verifyForgetPasswordRequest(string $email, string $code)
    {
        $dbConn = $this->db->getDBConn();

        $dbConn->beginTransaction();
        try{
            $query = "select * from forget_password where email=? and code=? and expires_at<=current_timestamp and used=false;";
            $prepare = $dbConn->prepare($query);
            $prepare->execute([$email, $code]);

            $data = $prepare->fetch();
            
            if(empty($data) || $data == null){
                throw new Exception("Código inválido ou já utilizado", 400);
            }else{
                $query = "update forget_password set used=true where email=? and codigo=?";
                $prepare = $dbConn->prepare($query);
                $prepare->execute([$email, $code]);

                if($prepare->rowCount() > 0){
                    $dbConn->commit();
                    return true;
                }
                else{
                    throw new Exception("Houve um problema para processar a solicitação, tente novamente!", HttpStatus::HTTP_INTERNAL_SERVER_ERROR->value);
                }
            }
        }catch(Exception $ex){
            $dbConn->rollBack();
            throw $ex;
        }
    }
}
