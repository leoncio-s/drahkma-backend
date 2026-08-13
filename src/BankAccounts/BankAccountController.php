<?php

namespace App\BankAccounts;

use App\BankAccounts\BankAccounts;
use App\BankAccounts\BankAccountsService;
use App\Utils\Http\Autenticated;
use App\Utils\Http\HttpStatus;
use App\Utils\Http\Request;
use App\Utils\Http\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/v1/banks', name:'bank_accounts_')]
class BankAccountController {

    private BankAccountsService $service;
    public function __construct(BankAccountsService $service) {
        $this->service = $service;
    }

    #[Route('', methods:['POST'], name:'create')]
    public function create(){
        if(Autenticated::autenticated()){
            $data = Request::getAll();
            $data['user'] = Autenticated::getUserAuth()['id'];
            $ret = $this->service->create($data);
            if($ret instanceof BankAccounts){
                return new JsonResponse($ret->toArray(), HttpStatus::HTTP_CREATED->value);
            }else{
                return new JsonResponse($ret, HttpStatus::HTTP_BAD_REQUEST->value);
            }
        }
    }

    #[Route('', methods:['GET'], name:'list')]
    public function list(){
        if(Autenticated::autenticated()){
            $user_id = Autenticated::getUserAuth()['id'];
            $data = $this->service->read($user_id);
            if($data==null){
                return new JsonResponse([], HttpStatus::HTTP_NO_CONTENT->value);
            }else{
                return new JsonResponse($data);
            }
        }
        return new JsonResponse(["message" => "ok"]);
    }

    public function get(int $id){
        if(Autenticated::autenticated()){
            
        }
    }

    #[Route('', methods:['PUT'], name:'update')]
    public function update(){
        if(Autenticated::autenticated()){
            $data = Request::getAll();
            if(isset($data['id'])){
                $data['user'] = Autenticated::getUserAuth()['id'];

                $ret = $this->service->update($data);
                if($ret instanceof BankAccounts){
                    return new JsonResponse($ret->toArray());
                }
                return new JsonResponse($ret, HttpStatus::HTTP_BAD_REQUEST->value);
            }else{
                return new JsonResponse([], HttpStatus::HTTP_BAD_REQUEST->value);
            }
            
        }
    }

    #[Route('/{id}', methods:['DELETE'], name:'delete', requirements:["id"=>"[0-9]+"])]
    public function delete(int $id){
        if(Autenticated::autenticated()){
            $data = [
                'id' => $id,
                'user' => Autenticated::getUserAuth()['id']
            ];

            $retSrv = $this->service->delete($data);

            if(isset($retSrv['error'])){
                return new JsonResponse($retSrv, HttpStatus::HTTP_BAD_REQUEST->value);
            }elseif($retSrv){
                return Response::json([]);
            }else{
                return new JsonResponse([$retSrv], HttpStatus::HTTP_INTERNAL_SERVER_ERROR->value);
            }
            //
        }
    }
}