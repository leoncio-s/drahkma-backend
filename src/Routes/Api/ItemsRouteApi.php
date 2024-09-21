<?php

namespace Routes\Api;

use App\Database\Databases;
use App\Items\ItemsRepository;
use App\Items\ItemsService;
use Bramus\Router\Router;
use controllers\ItemsController;
use Routes\ApiRoute;

class ItemsRouteApi extends ApiRoute{

    private ItemsService $service;
    private ItemsRepository $repo;

    public function __construct(Databases $db, Router $router) {
        $this->repo = new ItemsRepository($db);
        $this->service = new ItemsService($this->repo);

        parent::__construct($db, $router);
    }

    public function route()
    {
        $this->router->mount(API_ROUTE . "/item", function(){
            $controller = new ItemsController($this->service);
            $this->router->get("/", fn()=>$controller->getAll());
            $this->router->post("/", fn()=>$controller->create());
            $this->router->get("/inflow", fn()=>$controller->inflow());
            $this->router->get("/outflow", fn()=>$controller->outflow());
            $this->router->get("/amounts", fn()=>$controller->amounts());
        });
    }
}