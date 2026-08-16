<?php

namespace App\Categories;

use App\Categories\Categories;
use App\Categories\CategoriesService;
use App\Utils\Http\Autenticated;
use App\Utils\Http\HttpStatus;
use App\Utils\Http\Request;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/v1/categories", name:"categories_")]
class CategoriesController
{

    public function __construct(private CategoriesService $service, private LoggerInterface $logger)
    {}

    #[Route("", name:"save", methods:['POST'])]
    public function save()
    {
        if (Autenticated::autenticated()) {
            $data = Request::getAll();
            $data['user'] = Autenticated::getUserAuth()['id'];
            $ret = $this->service->create($data);
            if ($ret instanceof Categories) {
                return new JsonResponse($ret->toArray(), HttpStatus::HTTP_CREATED->value);
            } else {
                return new JsonResponse($ret, HttpStatus::HTTP_BAD_REQUEST->value);
            }
        }
    }

    #[Route("", name:"get_user", methods:['GET'])]
    public function getByUser()
    {
        if (Autenticated::autenticated()) {
            $user_id = Autenticated::getUserAuth()['id'];
            $data = $this->service->read($user_id);
            if ($data == null) {
                return new JsonResponse([], HttpStatus::HTTP_NO_CONTENT->value);
            } else {
                return new JsonResponse($data);
            }
        }
    }

    #[Route("/all", name:"get_all", methods:['GET'])]
    public function getAll()
    {
        if (Autenticated::autenticated()) {
            $user_id = Autenticated::getUserAuth()['id'];
            $data = $this->service->readAllByUser($user_id);
            if ($data == null) {
                return new JsonResponse([], HttpStatus::HTTP_NO_CONTENT->value);
            } else {
                return new JsonResponse($data);
            }
        }
    }


    #[Route("", name:"update", methods:['PUT'])]
    public function update()
    {
        try{
            if (Autenticated::autenticated()) {
                $data = Request::getAll();
                if (isset($data['id']) && isset($data['description'])) {
                    $data['user'] = Autenticated::getUserAuth()['id'];

                    $ret = $this->service->update($data);
        
                    if($ret instanceof Categories)
                    {
                        return new JsonResponse($ret->toArray());
                    }else  if (isset($ret['error'])) {
                        return new JsonResponse($ret, HttpStatus::HTTP_BAD_REQUEST->value);
                    }
                    return new JsonResponse($ret, 422);
                } else {
                    return new JsonResponse([], HttpStatus::HTTP_BAD_REQUEST->value);
                }
            }

        }catch(Exception $e)
        {
            $this->logger->error($e->getMessage(), $e->getTrace());
            throw new Exception("Erro ao processar a solicitação", 500, $e);
        }
    }

    #[Route("/{id}", name:"delete", methods:['DELETE'])]
    public function delete(int $id)
    {
        if (Autenticated::autenticated()) {
            $data = [
                'id' => $id,
                'user' => Autenticated::getUserAuth()['id']
            ];

            $retSrv = $this->service->delete($data);

            if (isset($retSrv['error'])) {
                return new JsonResponse($retSrv, HttpStatus::HTTP_BAD_REQUEST->value);
            } elseif ($retSrv) {
                return new JsonResponse([]);
            } else {
                return new JsonResponse([$retSrv], HttpStatus::HTTP_INTERNAL_SERVER_ERROR->value);
            }
            //
        }
    }
}
