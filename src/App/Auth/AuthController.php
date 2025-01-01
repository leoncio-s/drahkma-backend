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
        if(isset($req['email'])){
            $sendEmail = $this->service->forgetPasswordRequest($req['email']);
            if($sendEmail){
                return Response::json([]);
            }else{
                return Response::json(["error" => "Ocorreu algum problema com a solicitação. Tente novamente em outro momento"], HttpStatus::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            throw new Exception("Campo e-mail não localizado. Tente novamente!", 400);
        }
    }

    public function forgetPasswordVerify(string $email){

        $req = Request::getAll();
        if(isset($req['code'])){
            $verify = $this->service->forgetPasswordVerify(email:$email, code:$req['code']);
            if($verify){
                return Response::json([]);
            }else{
                return Response::json(["error" => "Ocorreu algum problema com a solicitação. Tente novamente em outro momento"], HttpStatus::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            throw new Exception("Campo e-mail não localizado. Tente novamente!", 400);
        }
    }
}