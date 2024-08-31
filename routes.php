<?php

// use Routes\Api\UsersRouteApi;

use Routes\Api\AuthRouteApi;
use Routes\Api\BankAccountsRouteApi;
use Routes\Api\CardsRouteApi;
use Routes\Api\UsersRouteApi;

$api = API_ROUTE;

// $router = $_SERVER['REQUEST_URI'];
// $method = $_SERVER['REQUEST_METHOD'];

// $userRoute = new UsersRouteApi($DB_CONNECTION, $router, $method);

// switch($router){
//     case $api . '/user':
//         $userRoute->route();
//         break;
//     default:
//         http_response_code(404);
//         break;
// }
$router = new \Bramus\Router\Router();


$userRoute = new UsersRouteApi($DB_CONNECTION, $router);
$userRoute->route();

$authRouter = new AuthRouteApi($DB_CONNECTION, $router);
$authRouter->route();

$banksRouter = new BankAccountsRouteApi($DB_CONNECTION, $router);
$banksRouter->route();

$cardsRouter = new CardsRouteApi($DB_CONNECTION, $router);
$cardsRouter->route();


$router->run();