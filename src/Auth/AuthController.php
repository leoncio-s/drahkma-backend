<?php

namespace App\Auth;

use App\Utils\Http\HttpStatus;
use App\Utils\Http\Request;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/v1/auth', name:"auth_")]
class AuthController{
    
    private AuthService $service;
    public function __construct(AuthService $userServices)
    {
        $this->service = $userServices;
    }

    #[Route("/login", name:"login", methods:["POST"])]
    public function login()
    {
        $req = Request::getAll();

        $ret = $this->service->login($req['email'], $req['password']);
        if(isset($ret['errors'])){
            return new JsonResponse($ret, $ret['errorCode']);
        }else{
            return new JsonResponse($ret, HttpStatus::HTTP_OK->value);
        }
    }

    #[Route("/forget-password", name:"forget_password", methods:["POST"])]
    public function forgetPassword(){
        $req = Request::getAll();
        // var_dump($req);
        if(isset($req['email'])){
            $sendEmail = $this->service->forgetPasswordRequest($req['email']);
            if($sendEmail){
                return new JsonResponse(["message"=> "Enviamos um e-mail para " . $req['email'] . " com o código para redefinição da senha."]);
            }else{
                return new JsonResponse(["error" => "Ocorreu algum problema com a solicitação. Tente novamente em outro momento"], HttpStatus::HTTP_INTERNAL_SERVER_ERROR->value);
            }
        }else{
            throw new Exception("Campo e-mail não localizado. Tente novamente!", 400);
        }
    }

    #[Route("/forget-password/{email}", name:"forget_password_new_password", methods:["POST"])]
    public function newPassword(string $email){

        $req = Request::getAll();
        $resp = $this->service->newPassword($email, $req);
        if($resp){
            return new JsonResponse(["message"=> "Senha alterada com sucesso."], 200);
        }else{
            return new JsonResponse(["error"=> "Não foi possível realizar a alteração. Tente novamente."], HttpStatus::HTTP_INTERNAL_SERVER_ERROR->value);
        }
    }
}