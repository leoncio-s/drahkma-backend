<?php

namespace Routes;

use App\Database\Databases;
use Bramus\Router\Router;

abstract class WebRoute{
    protected Router $router;
    protected string $defaultRoute = "/";
    public function __construct(Router $route) {
        header('Content-Type: text/html');
        $this->router = $route;
    }

    // abstract public function route(string $route, string $method);
    abstract public function route();
}