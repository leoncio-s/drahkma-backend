<?php

namespace App\Auth;

use App\Exceptions\EmailInvalidatedException;
use App\Exceptions\InvalidEmailOrPasswordException;
use App\Exceptions\UserNotFoundException;
use App\Interfaces\RepositoryInterface;
use App\Interfaces\ServicesInterface;
use App\Users\User;
use App\Users\UserRepository;
use App\Users\UserServices;
use App\Utils\Email\ForgetPasswordEmail;
use App\Utils\GenerateTokensUtils;
use App\Utils\Http\HttpStatus;
use App\Utils\JWTTokenUtils;
use App\Utils\PasswordUtils;
use Exception;

class AuthService
 {

    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function login(?string $email, ?string $password)
    {

        if ($email == null || $password == null)
            throw new InvalidEmailOrPasswordException();

        $user = $this->repository->getByEmail($email);

        if ($user == null) {
            sleep(2);
            throw new InvalidEmailOrPasswordException();
        } else if ($user->getEmailVerifiedAt() == null) {
            UserServices::generateEmailVerification($user);
            throw new EmailInvalidatedException("Email não validado.", 400);
        } elseif ($user->getEmailVerifiedAt() != null && !$user->getActived()) {
            return ['error' => 'User not allowed', 'errorCode' => HttpStatus::HTTP_FORBIDDEN];
        } elseif ($user instanceof User) {
            if (PasswordUtils::compare($password, $user->getPassword())) {
                if(password_needs_rehash($user->getPassword(), PASSWORD_BCRYPT))
                {
                    $this->repository->updatePassword($user->getId(), $password);
                }
                $token = JWTTokenUtils::generate($user);
                return ["token" => $token, "user" => $user->toArray()];
            }
        }

        throw new InvalidEmailOrPasswordException();
    }

    public function forgetPasswordRequest(string $email)
    {
        $user = $this->repository->getByEmail($email);

        if($user instanceof User){
            $code = GenerateTokensUtils::generateRandomTokenForgetPassword();
            $data = $this->repository->generateForgetPasswordRequest($user->getId(), $code);
            if(gettype($data) === "array"){
                return ForgetPasswordEmail::sendEmailForgetPassword($user->getEmail(), $user->getFullName(), $data["code"],  $data["expires_at"]);
            }elseif($data){
                return ForgetPasswordEmail::sendEmailForgetPassword($user->getEmail(), $user->getFullName(), $code);
            }else
            {
                return null;
            }
        }
        throw new UserNotFoundException("Houve um problema para realizar a solicitação. Verifique os dados e tente novamente!", 400);
    }

    public function newPassword(string $email, array $data){
        if(isset($data['code']) && isset($data['password']) && isset($data['confpassword']))
        {
            if($data['password'] != $data['confpassword'])
            {
                throw new Exception("Senhas incompatíveis", 400);
            }
            $user = $this->repository->getByEmail($email);
            $verify = $this->forgetPasswordVerify($user->getId(), code:$data['code']);
            if($verify){
                $nUser = $this->repository->updatePassword($user->getId(), $data['password']);
                if($nUser instanceof User){
                    return true;
                }else{
                    return false;
                }
            }else{
                throw new Exception("Código inválido.", 400);
            }
        }else{
            throw new Exception("Os campos de código, senha e confirmação de senha são obrigatórios.", 400);
        }
    }

    private function forgetPasswordVerify(int $idUser, string $code)
    {
        return $this->repository->verifyForgetPasswordRequest($idUser, $code);
    }

}