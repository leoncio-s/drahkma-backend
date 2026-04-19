<?php

namespace Routes\Web;

use Bramus\Router\Router;
use Routes\WebRoute;

class FrontEndRoute extends WebRoute{
    public function __construct(Router $router) {
        parent::__construct($router);
    }
    public function route(){
        $this->router->get("/", function(){
            // $file = fopen('./../../../front/index.html', "r");
            // var_dump($file);
            // require __DIR__ . '/../../../public/frontend/index.html';
            // echo
            // echo "Hello World";
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: /frontend");
        });

        $this->router->get("/hello-world", function(){
            // $file = fopen('./../../../front/index.html', "r");
            // var_dump($file);
            // require './../../../front/index.html';
            // echo
            // echo "Hello World";
            echo("hello world");
        });
    }
}