<?php

namespace App\Cards;

use App\Cards\Cards;
use App\Cards\CardsService;
use App\Utils\Http\Autenticated;
use App\Utils\Http\HttpStatus;
use App\Utils\Http\Request;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/v1/cards", name:"cards_")]
class CardsController
{

    // private CardsService $service;
    public function __construct(private CardsService $service, private ?LoggerInterface $logger = null){}
    // {
        // $this->service = $service;
    // }

    #[Route("", name:"list", methods:["GET"])]
    public function list()
    {
        if (Autenticated::autenticated()) {
            $user = Autenticated::getUserAuth();
            $data = $this->service->read($user['id']);
            if ($data == null) {
                return new JsonResponse([], HttpStatus::HTTP_NO_CONTENT->value);
            }
            return new JsonResponse($data);
        }
    }

    #[Route("", name:"save", methods:["POST"])]
    public function save()
    {
        if (Autenticated::autenticated()) {
            $data = Request::getAll();
            $user = Autenticated::getUserAuth();
            if ($user != null) {
                $data['user'] = $user['id'];
                $ret = $this->service->create($data);
                if ($ret instanceof Cards) {
                    return new JsonResponse($ret->toArray(), HttpStatus::HTTP_CREATED->value);
                } else {
                    return new JsonResponse($ret, HttpStatus::HTTP_BAD_REQUEST->value);
                }
            }
        }
    }

    #[Route("", name:"update", methods:["PUT"])]
    public function update()
    {
        if (Autenticated::autenticated()) {
            $data = Request::getAll();
            $user = Autenticated::getUserAuth();
            $data['user'] = $user['id'];
            $ret = $this->service->update($data);
            // var_dump($ret);
            if ($ret instanceof Cards) {
                return new JsonResponse($ret->toArray());
            } else {
                return new JsonResponse((array)$ret, HttpStatus::HTTP_BAD_REQUEST->value);
            }
        }
    }

    #[Route("/{id}", name:"delete", methods:["DELETE"])]
    public function delete(int $id)
    {
        try{
            if (Autenticated::autenticated()) {
            $user = Autenticated::getUserAuth();
            $data = [
                'id' => $id,
                'user' => $user['id']
            ];
            $ret = $this->service->delete($data);
            if (isset($ret["errors"])) {
                return new JsonResponse((array) $ret, HttpStatus::HTTP_BAD_REQUEST->value);
            } else {
                return new JsonResponse([]);
            }
        }
        }catch (Exception $e)
        {
            $this->logger->error($e->getMessage(), $e->getTrace());
            return new JsonResponse(["erro"=>$e->getMessage()], 400);
        }
    }
}
