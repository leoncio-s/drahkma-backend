<?php

namespace App\Cards;

use App\Cards\Cards;
use App\Cards\CardsService;
use App\Utils\Http\Autenticated;
use App\Utils\Http\HttpStatus;
use App\Utils\Http\Request;
use App\Utils\Http\Response;

class CardsController
{

    private CardsService $service;
    public function __construct(CardsService $service)
    {
        $this->service = $service;
    }

    public function getAll()
    {
        if (Autenticated::autenticated()) {
            $user = Autenticated::getUserAuth();
            $data = $this->service->read($user['id']);
            if ($data == null) {
                return Response::json([], HttpStatus::HTTP_NO_CONTENT);
            }
            return Response::json($data);
        }
    }

    public function create()
    {
        if (Autenticated::autenticated()) {
            $data = Request::getAll();
            $user = Autenticated::getUserAuth();
            if ($user != null) {
                $data['user'] = $user['id'];
                $ret = $this->service->create($data);
                if ($ret instanceof Cards) {
                    return Response::json($ret->toArray(), HttpStatus::HTTP_CREATED);
                } else {
                    return Response::json($ret, HttpStatus::HTTP_BAD_REQUEST);
                }
            }
        }
    }

    public function update()
    {
        if (Autenticated::autenticated()) {
            $data = Request::getAll();
            $user = Autenticated::getUserAuth();
            $data['user'] = $user['id'];
            $ret = $this->service->update($data);
            // var_dump($ret);
            if ($ret instanceof Cards) {
                return Response::json($ret->toArray());
            } else {
                return Response::json((array)$ret, HttpStatus::HTTP_BAD_REQUEST);
            }
        }
    }

    public function delete(int $id)
    {
        if (Autenticated::autenticated()) {
            $user = Autenticated::getUserAuth();
            $data = [
                'id' => $id,
                'user' => $user['id']
            ];
            $ret = $this->service->delete($data);
            if (isset($ret["errors"])) {
                return Response::json((array) $ret, HttpStatus::HTTP_BAD_REQUEST);
            } else {
                return Response::json([]);
            }
        }
    }
}
