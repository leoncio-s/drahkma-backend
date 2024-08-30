<?php

namespace controllers;

use App\Users\UserServices;
use controllers\Http\HttpStatus;
use controllers\Http\Request;
use controllers\Http\Response;
use Exception;

class AuthController{
    
    private UserServices $service;
    public function __construct(UserServices $userServices)
    {
        $this->service = $userServices;
    }

    public function login(){
        try{
            $req = Request::getAll();
            $ret = $this->service->login($req['email'], $req['password']);
            if(isset($req['error'])){
                Response::json($ret, $ret['errorCode']);
            }else{
                Response::json($ret, HttpStatus::HTTP_OK);
            }
        }catch(Exception $e){
            http_response_code(500);
            echo "erro ao processar requição";
        }
    }
}