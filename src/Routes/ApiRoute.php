<?php

namespace Routes;

use App\Database\Databases;
use Bramus\Router\Router;

abstract class ApiRoute{
    protected Router $router;
    protected string $api_route = API_ROUTE;
    public function __construct(Databases $db, Router $route) {
        header('Content-Type: application/json');
        $this->router = $route;
    }

    // abstract public function route(string $route, string $method);
    abstract public function route();
}