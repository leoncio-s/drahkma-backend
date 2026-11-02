<?php

namespace App\Features\BankAccounts\Infrastructure\Symfony\Controllers;

use App\Features\BankAccounts\Domain\Entity\BankAccounts;
use App\Exceptions\UnauthenticatedException;
use App\Features\BankAccounts\Application\Models\BankAccountsCreateDTO;
use App\Features\BankAccounts\Application\Models\BankAccountsUpdateDTO;
use App\Features\BankAccounts\Application\Service\BankAccountsService;
use App\Utils\Http\Autenticated;
use App\Utils\Http\HttpStatus;
use App\Utils\Http\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/v1/banks', name:'bank_accounts_')]
class BankAccountController extends AbstractController{

    private BankAccountsService $service;
    public function __construct(BankAccountsService $service) {
        $this->service = $service;
    }

    #[Route('', methods:['POST'], name:'create', format:"json")]
    public function save(
        #[MapRequestPayload] BankAccountsCreateDTO $bankAccountDTO
        ): Response
    {
        if(Autenticated::autenticated()){
            $bankAccountDTO->setUser(Autenticated::getUserAuth()['id']);
            $ret = $this->service->create($bankAccountDTO);
            if($ret instanceof BankAccounts){
                return $this->json($ret, HttpStatus::HTTP_CREATED->value);
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
    public function update(
        #[MapRequestPayload] BankAccountsUpdateDTO $bankAccountsDTO
    ) : Response
    {
        if(Autenticated::autenticated()){
            if($bankAccountsDTO->getId() !== null){
                $bankAccountsDTO->setUserId(Autenticated::getUserAuth()['id']);
                $ret = $this->service->update($bankAccountsDTO);
                if($ret instanceof BankAccounts){
                    return $this->json($ret);
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