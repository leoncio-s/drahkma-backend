<?php

namespace App\Users;

use App\Interfaces\ServicesInterface;
use App\Users\UserRepository;
use App\Utils\JWTTokenUtils;
use App\Utils\PasswordUtils;
use App\Utils\Utils;
use controllers\Http\HttpStatus;
use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;
use utils\email\EmailVerification;

class UserServices implements ServicesInterface{

    private UserRepository $repository;

    public function __construct(UserRepository $repository) {
        $this->repository = $repository;
    }

    public function create(array $data) : User | array | null {
        $validate = User::objValidate($data);
        $emailValidate = $this->repository->getByEmail($data['email']);

        if($emailValidate instanceof User){
            $validate['data'] = $data;
            $validate['errors']['email'] = 'Has exist account with this email.';
            return $validate;
        }else if(isset($validate['errors'])){
            return $validate;
        }
        $user = $this->repository->save($validate['data']);
        if($user instanceof User){
            return $user;
        }
        return $user;
    }

    public function read(string $id) : User | array | null{
        return $this->repository->get($id);
    }

    public function update(array $data) : ?User{
        return $this->repository->update($data);
    }

    public function delete($data) : bool | null{
        return $this->repository->delete($data['id']);
    }

    public function userByEmailToLogin(String $email) : User | null{
        $user = $this->repository->getByEmail($email);
        if($user != null && $user['actived']){
            return $user;
        }
        return null;
    }


    public function login(string $email, string $password){
        $user = $this->repository->getByEmail($email);
        
        if($user == null){
            // throw new Exception("User not found");
            return ['error' => 'User not found', 'errorCode' => HttpStatus::HTTP_NOT_FOUND ];
        }else if($user->getEmailVerifiedAt() == null){
            $this->generateEmailVerification($user);
            return ['error' => 'This e-mail has not verified', 'errorCode' => HttpStatus::HTTP_BAD_REQUEST];
        }
        
        if(PasswordUtils::compare($password, $user->getPassword())){
            // $user->tokens()->delete();
            // $token = $user->createToken($email, ['user' => $user])->plainTextToken;
            $token = JWTTokenUtils::generate($user);
            return ["token"=>$token, "user" => $user->toArray()];
        } 
        return ["error"=>"Incorrect Email or Password", "errorCode" => HttpStatus::HTTP_BAD_REQUEST];
    }

    public function generateEmailVerification(User $user){
        $exp_At = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
        $exp_At->add(DateInterval::createFromDateString('4 hour'));
        $stringDate =  $exp_At->format('Y-m-d H:i:s T');
        try{
            $token = Utils::emailGenerateToken($user->getEmail(), $user->getId(), $stringDate);

            $ret = $this->repository->generateEmailVerification($user->getEmail(), $token, $stringDate);

            if($ret){
                $link = SERVER_HOST . API_ROUTE . '/user/email/verify/' . Utils::base64_url_encode($token);
                return EmailVerification::sendEmailVerificationNotification($user->getEmail(), link:$link, tokenExp:$stringDate, userName:$user->getFullName());
            }else{
                return false;
            }
        }catch(Exception $e){
            return ['error' => $e->getCode()];
        }
    }

    public function verifyEmailToken(string $token){
        return $this->repository->getEmailByVerifyToken(Utils::base64_url_decode($token));
    }
}