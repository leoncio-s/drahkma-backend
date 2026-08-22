<?php

namespace App\Users;

use App\Exceptions\UserNotFoundException;
use App\Users\UserRepository;
use App\Utils\Base64Utils;
use App\Utils\GenerateTokensUtils;
use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;
use App\Utils\Email\EmailVerification;
use App\Utils\PasswordUtils;
use InvalidArgumentException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Throwable;

class UsersServices
{

    public function __construct(private UserRepository $repository, private UrlGeneratorInterface $urlGenerator)
    { }

    public function create(array $data): User | array | null
    {
        $validate = User::validate($data);
        $emailValidate = $this->repository->getByEmail($data['email']);

        if ($emailValidate instanceof User) {
            $validate['errors']['email'] = 'Has exist account with this email.';
            return $validate["errors"];
        } else if (isset($validate['errors'])) {
            return $validate["errors"];
        }
        $user = $this->repository->save($validate['data']);
        if ($user instanceof User) {
            return $user;
        }
        return $user;
    }

    public function read(int $id) : User | array | null
    {
        $data = $this->repository->get($id);
        return $data;
    }

    public function update(array $data) : ?User
    {
        $data = $this->repository->update($data);
        return $data;
    }

    public function delete(array $data) : bool | null
    {
        return $this->repository->delete($data['id']);
    }

    public function userByEmailToLogin(String $email): User | null
    {
        $user = $this->repository->getByEmail($email);
        if ($user != null && $user['actived']) {
            return $user;
        }
        return null;
    }


    public function verifyEmailToken(string $token) : bool
    {
        return $this->repository->getEmailByVerifyToken(Base64Utils::base64url_decode($token));
    }

    public function generateEmailVerification(User $user) : Throwable | bool
    {
        $exp_At = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
        $exp_At->add(DateInterval::createFromDateString('4 hour'));
        $stringDate =  $exp_At->format('Y-m-d H:i:s T');
        try {
            $token = GenerateTokensUtils::emailGenerateToken($user->getEmail(), $user->getId(), $stringDate);

            $ret = $this->repository->generateEmailVerification($user->getEmail(), $token, $stringDate);

            if ($ret) {
                $tokenb64 = Base64Utils::base64url_encode($token);
                $link = $this->urlGenerator->generate("user_email_verify", [
                    "token"=> $tokenb64
                ], UrlGeneratorInterface::ABSOLUTE_URL);
                return EmailVerification::sendEmailVerificationNotification($user->getEmail(), $link, $stringDate, $user->getFullName());
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function updatePassword(array $user, string $password, string $nPassword, string $cfNPassword) : Throwable | bool
    {
        $user = $this->repository->getByEmail($user["email"]);
        if($user == null)
        {
            throw new UserNotFoundException();
        }
        if($cfNPassword <> $nPassword)
        {
            throw new InvalidArgumentException("campos new_password e conf_new_password devem possui o mesmo valor", 422);
        }
        if(!PasswordUtils::compare($password, $user->getPassword())){
            throw new InvalidArgumentException("senha informada não confere", 422);
        }
        if(PasswordUtils::compare($nPassword, $user->getPassword())){
            throw new InvalidArgumentException("Nova senha não pode ser igual a atual", 422);
        }

        $user = $this->repository->updatePassword($user->getId(), $nPassword);
        if($user instanceof User)
        {
            return true;
        }else{
            return false;
        }
    }
}
