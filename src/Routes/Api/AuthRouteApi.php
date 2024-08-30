<?php

namespace Routes\Api;

use App\Database\Databases;
use App\Users\UserServices;
use App\Users\UserRepository;
use Bramus\Router\Router;
use controllers\AuthController;
use Routes\ApiRoute;

class AuthRouteApi extends ApiRoute{

    private UserServices $service;
    private UserRepository $repo;

    public function __construct(Databases $db, Router $router) {
        $this->repo = new UserRepository($db);
        $this->service = new UserServices($this->repo);

        parent::__construct($db, $router);
    }

    public function route(){
        $controller = new AuthController($this->service);

        $default = API_ROUTE . '/auth';
        $this->router->post($default . '/login', fn()=>$controller->login());
    }

}