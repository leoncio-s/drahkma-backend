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
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: /frontend/");
        });

        $this->router->get("/frontend/{any}", function(string $any){
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: /frontend/#" .$any);
        });

        $this->router->get("/hello-world", function(){
            echo("hello world");
        });
    }
}