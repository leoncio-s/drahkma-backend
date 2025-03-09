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
        $sql = "SELECT * FROM users WHERE id=:id";
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
        $sql = "UPDATE users SET password=? , update_at=now() where id=? ;";
        $uAt =  self::get($id);
        $uAt->setPassword($password);

        $conn = $this->db->getDBConn();
        $conn->beginTransaction();
        try{
            $prepare = $conn->prepare($sql);
            $prepare->execute([$uAt->getPassword(), $uAt->getId()]);

            if($prepare->rowCount()==1){
                $conn->commit();
                $ret = $this->get($uAt->getId());
                return $ret;
            }else{
                $conn->rollBack();
                throw new Exception("Não foi possível realizar a atualização");
            }

        }catch(Exception $ex){
            $conn->rollBack();
            throw $ex;
        }
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

        if(!$dbConn->inTransaction())
            $dbConn->beginTransaction();
        try{
            $query = "delete from forget_password where user=? and expires_at < current_timestamp and used=false;";
            $prepare = $dbConn->prepare($query);
            $prepare->execute([$idUser]);

            $query = "insert into forget_password(user, code) values (?, ?)";
            $prepare = $dbConn->prepare($query);
            $prepare->execute([$idUser, $code]);
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

    public function verifyForgetPasswordRequest(string $idUser, string $code)
    {
        $dbConn = $this->db->getDBConn();

        $dbConn->beginTransaction();
        try{
            $query = "select * from forget_password where user=? and code=? and expires_at>=current_timestamp and used=false;";
            $prepare = $dbConn->prepare($query);
            $prepare->execute([$idUser, $code]);

            $data = $prepare->fetch();
            
            if(empty($data) || $data == null){
                throw new Exception("Código inválido", 400);
            }else{
                $query = "update forget_password set used=true where user=? and code=?";
                $prepare = $dbConn->prepare($query);
                $prepare->execute([$idUser, $code]);

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
