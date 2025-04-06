<?php

namespace App\Auth;

use App\Utils\Http\HttpStatus;
use App\Utils\Http\Request;
use App\Utils\Http\Response;
use Exception;

class AuthController{
    
    private AuthService $service;
    public function __construct(AuthService $userServices)
    {
        $this->service = $userServices;
    }

    public function login()
    {
        $req = Request::getAll();

        $ret = $this->service->login($req['email'], $req['password']);
        if(isset($ret['errors'])){
            return Response::json($ret, $ret['errorCode']);
        }else{
            return Response::json($ret, HttpStatus::HTTP_OK);
        }
    }

    public function forgetPassword(){
        $req = Request::getAll();
        // var_dump($req);
        if(isset($req['email'])){
            $sendEmail = $this->service->forgetPasswordRequest($req['email']);
            if($sendEmail){
                return Response::json(["message"=> "Enviamos um e-mail para " . $req['email'] . " com o código para redefinição da senha."]);
            }else{
                return Response::json(["error" => "Ocorreu algum problema com a solicitação. Tente novamente em outro momento"], HttpStatus::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            throw new Exception("Campo e-mail não localizado. Tente novamente!", 400);
        }
    }

    public function newPassword(string $email){

        $req = Request::getAll();
        if($this->service->newPassword($email, $req)){
            return Response::json(["message"=> "Senha alterada com sucesso."], 200);
        }else{
            return Response::json(["message"=> "Não foi possível realizar a alteração. Tente novamente."], HttpStatus::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}