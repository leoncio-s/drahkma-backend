<?php

namespace App\Feature\BankAccounts\Infrastructure\Symfony\Controllers;

use App\Feature\BankAccounts\Domain\Entity\BankAccounts;
use App\Exceptions\UnauthenticatedException;
use App\Feature\BankAccounts\Application\Service\BankAccountsService;
use App\Utils\Http\Autenticated;
use App\Utils\Http\HttpStatus;
use App\Utils\Http\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/v1/banks', name:'bank_accounts_')]
class BankAccountController extends AbstractController{

    private BankAccountsService $service;
    public function __construct(BankAccountsService $service) {
        $this->service = $service;
    }

    #[Route('', methods:['POST'], name:'create')]
    public function save(): Response
    {
        if(Autenticated::autenticated()){
            $data = Request::getAll();
            $data['user'] = Autenticated::getUserAuth()['id'];
            $ret = $this->service->create($data);
            if($ret instanceof BankAccounts){
                return $this->json($ret->toArray(), HttpStatus::HTTP_CREATED->value);
            }else{
                return $this->json($ret, HttpStatus::HTTP_BAD_REQUEST->value);
            }
        }
        throw new UnauthenticatedException();
    }

    #[Route('', methods:['GET'], name:'list')]
    public function list() : Response
    {
        if(Autenticated::autenticated()){
            $user_id = Autenticated::getUserAuth()['id'];
            $data = $this->service->read($user_id);
            if($data==null){
                return $this->json([], HttpStatus::HTTP_NO_CONTENT->value);
            }else{
                return $this->json($data);
            }
        }
        throw new UnauthenticatedException();
    }

    #[Route('', methods:['PUT'], name:'update')]
    public function update() : Response
    {
        if(Autenticated::autenticated()){
            $data = Request::getAll();
            if(isset($data['id'])){
                $data['user'] = Autenticated::getUserAuth()['id'];

                $ret = $this->service->update($data);
                if($ret instanceof BankAccounts){
                    return $this->json($ret->toArray());
                }
                return $this->json($ret, HttpStatus::HTTP_BAD_REQUEST->value);
            }else{
                return $this->json([], HttpStatus::HTTP_BAD_REQUEST->value);
            }
            
        }
        throw new UnauthenticatedException();
    }

    #[Route('/{id}', methods:['DELETE'], name:'delete', requirements:["id"=>"[0-9]+"])]
    public function delete(int $id) : Response
    {
        if(Autenticated::autenticated()){
            $data = [
                'id' => $id,
                'user' => Autenticated::getUserAuth()['id']
            ];

            $retSrv = $this->service->delete($data);

            if(isset($retSrv['error'])){
                return $this->json($retSrv, HttpStatus::HTTP_BAD_REQUEST->value);
            }elseif($retSrv){
                return $this->json([]);
            }else{
                return $this->json([$retSrv], HttpStatus::HTTP_INTERNAL_SERVER_ERROR->value);
            }
            //
        }
        throw new UnauthenticatedException();
    }
}