<?php

namespace Routes\Api;

use App\Cards\CardsRepository;
use App\Cards\CardsService;
use controllers\CardsController;
use Routes\ApiRoute;

class CardsRouteApi extends ApiRoute{
    public function route(){
        $this->router->mount(API_ROUTE . '/cards', function(){
            $repository = new CardsRepository($this->db);
            $service = new CardsService($repository);

            $controller = new CardsController($service);
            $this->router->get("/", fn()=>$controller->getAll());
            $this->router->post("/", fn()=>$controller->create());
        });
    }
}