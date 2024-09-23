<?php

namespace Routes\Api;

use App\Database\Databases;
use App\BankAccounts\BankAccountsRepository;
use App\BankAccounts\BankAccountsService;
use Bramus\Router\Router;
use controllers\BankAccountController;
use controllers\Http\Autenticated;
use Routes\ApiRoute;

class BankAccountsRouteApi extends ApiRoute{

    private BankAccountsService $service;
    private BankAccountsRepository $repo;

    public function __construct(Databases $db, Router $router) {
        $this->repo = new BankAccountsRepository($db);
        $this->service = new BankAccountsService($this->repo);

        parent::__construct($db, $router);
    }

    public function route()
    {
        $this->router->mount(API_ROUTE . "/banks", function(){
            $controller = new BankAccountController($this->service);
            $this->router->get("/", fn()=>$controller->getAll());
            $this->router->post("/", fn()=>$controller->create());
            $this->router->put('/', fn()=> $controller->update());
            $this->router->delete('/{id}', fn(int $id)=>$controller->delete($id));
        });
    }
}