<?php

namespace App\Items;

use App\Items\Items;
use App\Items\ItemsService;
use App\Utils\Http\Autenticated;
use App\Utils\Http\HttpStatus;
use App\Utils\Http\Request;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/v1/item", name:"items_")]
class ItemsController {

    private ItemsService $service;
    public function __construct(ItemsService $service) {
        $this->service = $service;
    }

    #[Route("", name:"save", methods:["POST"])]
    public function save(){
        if(Autenticated::autenticated()){
            $data = Request::getAll();
            $data['user'] = Autenticated::getUserAuth()['id'];
            $ret = $this->service->create($data);
            if($ret instanceof Items){
                return new JsonResponse($ret->toArray(), HttpStatus::HTTP_CREATED->value);
            }else{
                return new JsonResponse($ret, HttpStatus::HTTP_BAD_REQUEST->value);
            }
        }
    }

    #[Route("", name:"get", methods:["GET"])]
    public function get(){
        if(Autenticated::autenticated()){
            $user_id = Autenticated::getUserAuth()['id'];
            $data = $this->service->read($user_id);
            if($data==null){
                return new JsonResponse([], HttpStatus::HTTP_NO_CONTENT->value);
            }else{
                $nData = [];
                foreach($data as $dt){
                    $dt['date'] = $dt['date']->format('Ymd');
                    array_push($nData, $dt);
                }
                $data = null;
                // var_dump($nData);
                return new JsonResponse($nData);
            }
        }
    }

    #[Route("", name:"update", methods:["PUT"])]
    public function update(){
        if(Autenticated::autenticated()){
            $user = Autenticated::getUserAuth();
            $data = Request::getAll();
            $data['user'] = $user['id'];
            $ret = $this->service->update($data);
            if($ret instanceof Items){
                return new JsonResponse($ret->toArray());
            }
            return new JsonResponse($ret, HttpStatus::HTTP_INTERNAL_SERVER_ERROR->value);
        }
    }

    #[Route("/{id}", name:"delete", methods:["DELETE"])]
    public function delete(int $id){
        if(Autenticated::autenticated()){
            $user = Autenticated::getUserAuth();
            $data = [
                "user" => $user['id'],
                "id" => $id
            ];
            $ret = $this->service->delete($data);
            if($ret){
                return new JsonResponse(["message"=>"Content has deleted successfully"]);
            }elseif($ret==false){
                return new JsonResponse(["errors" => "Content not deleted"], HttpStatus::HTTP_BAD_REQUEST->value);
            }
            return new JsonResponse((array) $ret, HttpStatus::HTTP_INTERNAL_SERVER_ERROR->value);
        }
    }

    #[Route("/inflow", name:"inflow", methods:["GET"])]
    public function inflow(){
        if(Autenticated::autenticated()){
            if(isset($_GET['start_date'], $_GET['finish_date'])){
                $start = DateTime::createFromFormat('Ymd', $_GET['start_date']);
                $finish = DateTime::createFromFormat('Ymd', $_GET['finish_date']);

                $data = $this->service->getInflow($start, $finish);
                return new JsonResponse($data);
            }
            return new JsonResponse(null, HttpStatus::HTTP_BAD_REQUEST->value);
        }
    }

    #[Route("/outflow", name:"outflow", methods:["GET"])]
    public function outflow(){
        if(Autenticated::autenticated()){
            if(isset($_GET['start_date'], $_GET['finish_date'])){
                $start = DateTime::createFromFormat('Ymd', $_GET['start_date']);
                $finish = DateTime::createFromFormat('Ymd', $_GET['finish_date']);

                $data = $this->service->getOutflow($start, $finish);
                return new JsonResponse($data);
            }
            return new JsonResponse(null, HttpStatus::HTTP_BAD_REQUEST->value);
        }
    }

    #[Route("/amounts", name:"amounts", methods:["GET"])]
    public function amounts(){
        if(Autenticated::autenticated()){
            if(isset($_GET['start_date'], $_GET['finish_date'])){

                $start = DateTime::createFromFormat('Ymd', $_GET['start_date']);
                $finish = DateTime::createFromFormat('Ymd', $_GET['finish_date']);
                $group = isset($_GET['group']) ? ItemsGroupEnum::tryFrom($_GET["group"]) : null;

                $data = $this->service->getAmounts($start, $finish);

                return new JsonResponse($data);
            }
            return new JsonResponse([], HttpStatus::HTTP_BAD_REQUEST->value);
        }
        return null;
    }
}
