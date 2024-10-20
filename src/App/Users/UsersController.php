<?php

namespace App\Users;

use App\Users\User;
use App\Users\UserServices;
use App\Utils\Http\Autenticated;
use App\Utils\Http\Response;
use App\Utils\Http\HttpStatus as hS;
use App\Utils\Http\HttpStatus;
use App\Utils\Http\Request;
use Exception;
use PDOException;

use function App\Utils\Http\autenticated;

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
}