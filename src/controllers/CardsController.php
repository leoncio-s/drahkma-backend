<?php

namespace controllers;

use App\Cards\Cards;
use App\Cards\CardsService;
use controllers\Http\Autenticated;
use controllers\Http\HttpStatus;
use controllers\Http\Request;
use controllers\Http\Response;

class CardsController {

    private CardsService $service;
    public function __construct(CardsService $service) {
        $this->service = $service;
        
    }

    public function getAll(){
        if(Autenticated::autenticated()){
            $user = Autenticated::getUserAuth();
            $data = $this->service->read($user['id']);
            if($data==null){
                return Response::json([], HttpStatus::HTTP_NO_CONTENT);
            }
            return Response::json($data);
        }
    }

    public function create(){
        if(Autenticated::autenticated()){
            $data = Request::getAll();
            $user = Autenticated::getUserAuth();
            if($user != null){
                $data['user'] = $user['id'];
                $ret = $this->service->create($data);
                if($ret instanceof Cards){
                    return Response::json($ret->toArray(), HttpStatus::HTTP_CREATED);
                }else{
                    return Response::json($ret, HttpStatus::HTTP_BAD_REQUEST);
                }
            }
        }
    }
}