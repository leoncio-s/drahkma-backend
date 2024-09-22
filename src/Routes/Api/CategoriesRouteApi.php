<?php

namespace Routes\Api;

use App\Database\Databases;
use App\Categories\CategoriesRepository;
use App\Categories\CategoriesService;
use Bramus\Router\Router;
use controllers\BankAccountController;
use controllers\CategoriesController;
use controllers\Http\Autenticated;
use Routes\ApiRoute;

class CategoriesRouteApi extends ApiRoute{

    private CategoriesService $service;
    private CategoriesRepository $repo;

    public function __construct(Databases $db, Router $router) {
        $this->repo = new CategoriesRepository($db);
        $this->service = new CategoriesService($this->repo);

        parent::__construct($db, $router);
    }

    public function route()
    {
        $this->router->mount(API_ROUTE . "/categories", function(){
            $controller = new CategoriesController($this->service);
            $this->router->get("/", fn()=>$controller->getAll());
            $this->router->post("/", fn()=>$controller->create());
            $this->router->put('/', fn()=> $controller->update());
            $this->router->delete('/{id}', fn(int $id)=>$controller->delete($id));
        });
    }
}