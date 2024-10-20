<?php


namespace Routes\Api;

use App\Database\Databases;
use App\Users\UserServices;
use App\Users\UserRepository;
use App\Users\UsersController;
use Bramus\Router\Router;
use Routes\ApiRoute;

class UsersRouteApi extends ApiRoute{

    private UserServices $service;
    private UserRepository $repo;

    public function __construct(Databases $db, Router $router) {
        $this->repo = new UserRepository($db);
        $this->service = new UserServices($this->repo);

        parent::__construct($db, $router);
    }

   public function route(){

        $controller = new UsersController($this->service);

        $userRouter = $this->api_route . '/user';
        
        $this->router->get($userRouter, fn() => $controller->profile());
        $this->router->post($userRouter, fn()=> $controller->create());
        $this->router->get($userRouter . '/email/verify/{$token}', fn(string $token) => $controller->emailVerified($token));
        // $this->router->get($userRouter, fn()=>$controller->create($_POST));
   }

}
