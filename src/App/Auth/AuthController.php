<?php

namespace App\Auth;

use App\Users\UserServices;
use App\Utils\Http\HttpStatus;
use App\Utils\Http\Request;
use App\Utils\Http\Response;
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
            if(isset($ret['errors'])){
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