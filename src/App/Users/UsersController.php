<?php

namespace App\Users;

use App\Logging\Log;
use App\Users\User;
use App\Users\UserServices;
use App\Utils\Http\Autenticated;
use App\Utils\Http\Response;
use App\Utils\Http\HttpStatus as hS;
use App\Utils\Http\HttpStatus;
use App\Utils\Http\Request;
use Error;
use Exception;
use InvalidArgumentException;
use PDOException;

class UsersController{


    private UserServices $services;
    public function __construct(UserServices $userServices) {
        $this->services = $userServices;
    }


    public function profile(){
        
        if(Autenticated::autenticated()){
            $user = $this->services->read(Autenticated::getUserAuth()['id']);

            return Response::json($user->toArray(), HttpStatus::HTTP_OK);
        }
    }

    public function create()
    {
        try{

            $data = Request::getAll();

            $user = $this->services->create($data);
            if($user instanceof User){
                $this->services->generateEmailVerification($user);
                return Response::json($user->toArray(), hS::HTTP_CREATED);
            }else{
                return Response::json($user, hS::HTTP_BAD_REQUEST);
            }
        }catch(PDOException $e){
            return Response::json(['error' => $e->errorInfo], hS::HTTP_INTERNAL_SERVER_ERROR);
        }catch(Exception $e){
            return Response::json(['error' => $e->getCode()], hS::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get(int $id){
        $ret = $this->services->read($id);
        if($ret == null){
            http_response_code(404);
            echo "not found";
        }
        else
            return $ret->toJson();
    }

    public function emailVerified(string $token){
        if($this->services->verifyEmailToken($token)){
            http_response_code(301);
            header('location: /');
        }else{
            echo "Token inválido ou já expirou. Tente novamente!";
        }
    }

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
                return Response::json($data->toArray());
            }else{
                throw new InvalidArgumentException("campos email, fullname e phone_number são obrigatórios", 400);
            }
        }
    }

    public function updatePassword()
    {
        if(Autenticated::autenticated())
        {
            $user = Autenticated::getUserAuth();
            $data = Request::getAll();
            new Log(in_array(["password", "new_password", "conf_new_password"], $data));

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
                return Response::json();
            }else{
                throw new Exception("Houve um erro ao processar a solicitação. Tente novamente mais tarde ou contate o administrador do sistema", 500);
            }
        }
    }
}