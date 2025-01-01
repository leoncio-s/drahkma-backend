<?php

namespace App\Users;

use App\Interfaces\RepositoryInterface;
use App\Interfaces\ServicesInterface;
use App\Users\UserRepository;
use App\Utils\Base64Utils;
use App\Utils\GenerateTokensUtils;
use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;
use App\Utils\email\EmailVerification;

class UserServices implements ServicesInterface
{

    private static UserRepository $repository;

    public function __construct(RepositoryInterface $repository)
    {
        self::$repository = $repository;
    }

    public function create(array $data): User | array | null
    {
        $validate = User::validate($data);
        $emailValidate = self::$repository->getByEmail($data['email']);

        if ($emailValidate instanceof User) {
            $validate['data'] = $data;
            $validate['errors']['email'] = 'Has exist account with this email.';
            return $validate;
        } else if (isset($validate['errors'])) {
            return $validate;
        }
        $user = self::$repository->save($validate['data']);
        if ($user instanceof User) {
            return $user;
        }
        return $user;
    }

    public function read(int $id) : User | array | null
    {
        $data = self::$repository->get($id);
        return $data;
    }

    public function update(array $data) : ?User
    {
        return self::$repository->update($data);
    }

    public function delete($data) : bool | null
    {
        return self::$repository->delete($data['id']);
    }

    public function userByEmailToLogin(String $email): User | null
    {
        $user = self::$repository->getByEmail($email);
        if ($user != null && $user['actived']) {
            return $user;
        }
        return null;
    }


    public function verifyEmailToken(string $token)
    {
        return self::$repository->getEmailByVerifyToken(Base64Utils::base64_url_decode($token));
    }

    public static function generateEmailVerification(User $user)
    {
        $exp_At = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
        $exp_At->add(DateInterval::createFromDateString('4 hour'));
        $stringDate =  $exp_At->format('Y-m-d H:i:s T');
        try {
            $token = GenerateTokensUtils::emailGenerateToken($user->getEmail(), $user->getId(), $stringDate);

            $ret = self::$repository->generateEmailVerification($user->getEmail(), $token, $stringDate);

            if ($ret) {
                $link = SERVER_HOST . API_ROUTE . '/user/email/verify/' . Base64Utils::base64_url_encode($token);
                return EmailVerification::sendEmailVerificationNotification($user->getEmail(), $link, $stringDate, $user->getFullName());
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}
