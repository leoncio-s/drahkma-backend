<?php

namespace controllers;

use App\BankAccounts\BankAccounts;
use App\BankAccounts\BankAccountsService;
use controllers\Http\Autenticated;
use controllers\Http\HttpStatus;
use controllers\Http\Request;
use controllers\Http\Response;

class BankAccountController {

    private BankAccountsService $service;
    public function __construct(BankAccountsService $service) {
        $this->service = $service;
    }

    public function create(){
        if(Autenticated::autenticated()){
            $data = Request::getAll();
            $data['user'] = Autenticated::getUserAuth()['id'];
            $ret = $this->service->create($data);
            if($ret instanceof BankAccounts){
                return Response::json($ret->toArray(), HttpStatus::HTTP_CREATED);
            }else{
                return Response::json($ret, HttpStatus::HTTP_BAD_REQUEST);
            }
        }
    }

    public function getAll(){
        if(Autenticated::autenticated()){
            $user_id = Autenticated::getUserAuth()['id'];
            $data = $this->service->read($user_id);
            if($data==null){
                return Response::json([], HttpStatus::HTTP_BAD_REQUEST);
            }else{
                return Response::json($data);
            }
        }
    }

    public function get(int $id){
        if(Autenticated::autenticated()){
            
        }
    }

    public function update(int $id){
        if(Autenticated::autenticated()){
            
        }
    }
}