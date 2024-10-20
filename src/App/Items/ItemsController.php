<?php

namespace App\Items;

use App\Items\Items;
use App\Items\ItemsService;
use App\Logging\Log;
use App\Utils\Http\Autenticated;
use App\Utils\Http\HttpStatus;
use App\Utils\Http\Request;
use App\Utils\Http\Response;
use DateTime;

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

    public function update(){
        if(Autenticated::autenticated()){
            $user = Autenticated::getUserAuth();
            $data = Request::getAll();
            $data['user'] = $user['id'];
            $ret = $this->service->update($data);
            if($ret instanceof Items){
                return Response::json($ret->toArray());
            }
            return Response::json($ret, HttpStatus::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(int $id){
        if(Autenticated::autenticated()){
            $user = Autenticated::getUserAuth();
            $data = [
                "user" => $user['id'],
                "id" => $id
            ];
            $ret = $this->service->delete($data);
            if($ret){
                return Response::json();
            }elseif($ret==false){
                return Response::json(["errors" => "Content not deleted"], HttpStatus::HTTP_BAD_REQUEST);
            }
            return Response::json((array) $ret, HttpStatus::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function inflow(){
        if(Autenticated::autenticated()){
            if(isset($_GET['start_date'], $_GET['finish_date'])){
                $start = DateTime::createFromFormat('Ymd', $_GET['start_date']);
                $finish = DateTime::createFromFormat('Ymd', $_GET['finish_date']);

                $data = $this->service->getInflow($start, $finish);
                return Response::json($data);
            }
            return Response::json(null, HttpStatus::HTTP_BAD_REQUEST);
        }
    }

    public function outflow(){
        if(Autenticated::autenticated()){
            if(isset($_GET['start_date'], $_GET['finish_date'])){
                $start = DateTime::createFromFormat('Ymd', $_GET['start_date']);
                $finish = DateTime::createFromFormat('Ymd', $_GET['finish_date']);

                $data = $this->service->getOutflow($start, $finish);
                return Response::json($data);
            }
            return Response::json(null, HttpStatus::HTTP_BAD_REQUEST);
        }
    }

    public function amounts(){
        if(Autenticated::autenticated()){
            if(isset($_GET['start_date'], $_GET['finish_date'])){

                $start = DateTime::createFromFormat('Ymd', $_GET['start_date']);
                $finish = DateTime::createFromFormat('Ymd', $_GET['finish_date']);

                $data = $this->service->getAmounts($start, $finish);

                return Response::json($data);
            }
            return Response::json([], HttpStatus::HTTP_BAD_REQUEST);
        }
    }
}
