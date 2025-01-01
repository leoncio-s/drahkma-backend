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
use App\Utils\email\ForgetPasswordEmail;
use App\Utils\GenerateTokensUtils;
use App\Utils\Http\HttpStatus;
use App\Utils\JWTTokenUtils;
use App\Utils\PasswordUtils;

class AuthService implements ServicesInterface
 {

    private UserRepository $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function login(?string $email, ?string $password)
    {

        if ($email == null || $password == null)
            throw new InvalidEmailOrPasswordException("Email ou senha inválidos", 400);

        $user = $this->repository->getByEmail($email);

        if ($user == null) {
            throw new UserNotFoundException("Usuário não Encontrado", 404);
        } else if ($user->getEmailVerifiedAt() == null) {
            UserServices::generateEmailVerification($user);
            throw new EmailInvalidatedException("Email não validado.", 400);
        } elseif ($user->getEmailVerifiedAt() != null && !$user->getActived()) {
            return ['error' => 'User not allowed', 'errorCode' => HttpStatus::HTTP_FORBIDDEN];
        } elseif ($user instanceof User) {
            if (PasswordUtils::compare($password, $user->getPassword())) {
                $token = JWTTokenUtils::generate($user);
                return ["token" => $token, "user" => $user->toArray()];
            }
        }

        throw new InvalidEmailOrPasswordException("E-mail ou senha incorretos.", 400);
    }

    public function forgetPasswordRequest(string $email)
    {
        $user = $this->repository->getByEmail($email);

        if($user instanceof User){
            $code = GenerateTokensUtils::generateRandomTokenForgetPassword();
            if($this->repository->generateForgetPasswordRequest($user->getId(), $code)){
                return ForgetPasswordEmail::sendEmailForgetPassword($user->getEmail(), $user->getFullName(), $code);
            }else
            {
                return null;
            }
        }
        throw new UserNotFoundException("Email não localizado. Verifique!");
    }

    public function forgetPasswordVerify(string $email, string $code)
    {
        if($this->repository->verifyForgetPasswordRequest($email, $code)){
            return true;
        }
        return false;
    }

}