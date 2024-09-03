<?php

namespace controllers;

use App\Items\Items;
use App\Items\ItemsService;
use controllers\Http\Autenticated;
use controllers\Http\HttpStatus;
use controllers\Http\Request;
use controllers\Http\Response;

class ItemsController {

    private ItemsService $service;
    public function __construct(ItemsService $service) {
        $this->service = $service;
    }

    public function create(){
        if(Autenticated::autenticated()){
            $data = Request::getAll();
            $data['user'] = Autenticated::getUserAuth()['id'];
            $ret = $this->service->create($data);
            if($ret instanceof Items){
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
                return Response::json([], HttpStatus::HTTP_NO_CONTENT);
            }else{
                $nData = [];
                foreach($data as $dt){
                    $dt['date'] = $dt['date']->format('Ymd');
                    array_push($nData, $dt);
                }
                $data = null;
                // var_dump($nData);
                return Response::json($nData);
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