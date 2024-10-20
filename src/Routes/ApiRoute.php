<?php

namespace Routes;

use App\Database\Databases;
use App\Logging\Log;
use Bramus\Router\Router;
use App\Utils\Http\HttpStatus;
use App\Utils\Http\Response;

abstract class ApiRoute{
    protected Router $router;
    protected string $api_route = API_ROUTE;
    protected Databases $db;
    public function __construct(Databases $db, Router $route) {
        header('Content-Type: application/json');
        $this->router = $route;
        $this->db=$db;
    }

    // abstract public function route(string $route, string $method);
    abstract public function route();
}