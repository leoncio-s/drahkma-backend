<?php

use App\Database\MySqlDatabaseImpl;
use App\Services\UserServices;
use App\Users\UserRepository;

// require __DIR__ . '/src/app/*/*.php';
// require __DIR__ . '/src/app/Database/MySqlDbImpl.php';



// require_once __DIR__ . '/src/app' . '*' . '.php';

spl_autoload_register(function($className){
    include __DIR__ . "/src/" . str_replace("\\", "/", $className) . '.php';
});