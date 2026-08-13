<?php

namespace App\Users;

use App\Users\User;
use App\Users\UserServices;
use App\Utils\Http\Autenticated;
use App\Utils\Http\HttpStatus as hS;
use App\Utils\Http\HttpStatus;
use App\Utils\Http\Request;
use Exception;
use InvalidArgumentException;
use PDOException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/v1/user", name:"user_")]
class UsersController{


    private UserServices $services;
    public function __construct(UserServices $userServices) {
        $this->services = $userServices;
    }


    #[Route('', name:"profile", methods:["GET"])]
    public function profile(){
        
        if(Autenticated::autenticated()){
            $user = $this->services->read(Autenticated::getUserAuth()['id']);
            return new JsonResponse($user->toArray(), HttpStatus::HTTP_OK->value);
        }
    }

    #[Route('', name:"save", methods:["POST"])]
    public function save()
    {
        try{

            $data = Request::getAll();

            $user = $this->services->create($data);
            if($user instanceof User){
                $this->services->generateEmailVerification($user);
                return new JsonResponse($user->toArray(), hS::HTTP_CREATED->value);
            }else{
                return new JsonResponse($user, hS::HTTP_BAD_REQUEST->value);
            }
        }catch(PDOException $e){
            return new JsonResponse(['error' => $e->errorInfo], hS::HTTP_INTERNAL_SERVER_ERROR->value);
        }catch(Exception $e){
            return new JsonResponse(['error' => $e->getCode()], hS::HTTP_INTERNAL_SERVER_ERROR->value);
        }
    }

    #[Route('/{id}', name:"get", methods:["GET"])]
    public function get(int $id){
        $ret = $this->services->read($id);
        if($ret == null){
            throw new NotFoundHttpException("Item not found");
        }else
            return new JsonResponse($ret->toArray());
    }

    #[Route('/email/verify/{$token}', name:"email_verify", methods:["GET"])]
    public function emailVerified(string $token){
        if($this->services->verifyEmailToken($token)){
            http_response_code(301);
            header('location: /');
        }else{
            echo "Token inválido ou já expirou. Tente novamente!";
        }
    }

    #[Route('', name:"update", methods:["PUT"])]
    public function update(){
        if(Autenticated::autenticated())
        {
            $user = $this->services->read(Autenticated::getUserAuth()['id']);
            $data = Request::getAll();
            if(isset($data['fullname']) && isset($data['phone_number']) && isset($data['email']) && $data['email']==$user->getEmail())
            {
                $user->setFullName($data['fullname']);
                $user->setPhoneNumber($data['phone_number']);
                $data=$this->services->update($user->toArray());
                return new JsonResponse($data->toArray());
            }else{
                throw new InvalidArgumentException("campos email, fullname e phone_number são obrigatórios", 400);
            }
        }
    }

    #[Route('/password', name:"update_password", methods:["PUT"])]
    public function updatePassword()
    {
        if(Autenticated::autenticated())
        {
            $user = Autenticated::getUserAuth();
            $data = Request::getAll();

            if(!isset($data["password"]) && !isset($data["new_password"]) && !isset($data["conf_new_password"]) ){
                throw new InvalidArgumentException("campos password, new_password e conf_new_password são obrigatórios", 422);
            }
            if($data["new_password"] <>$data["conf_new_password"])
            {
                throw new InvalidArgumentException("campos new_password e conf_new_password devem possui o mesmo valor", 422);
            }
            $returnServ = $this->services->updatePassword($user, $data['password'], $data['new_password'], $data['conf_new_password']);
            if($returnServ)
            {
                return new JsonResponse(["message"=>"Password has update successffully"]);
            }else{
                throw new Exception("Houve um erro ao processar a solicitação. Tente novamente mais tarde ou contate o administrador do sistema", 500);
            }
        }
    }
}