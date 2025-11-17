<?php

function cors() {
    
    if(parse_ini_file(INI_FILE, true)['APP']['ENV']=='dev'){
        header("Access-Control-Allow-Origin: *");
    }else{
        header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_HOST']);
    }
    
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    
}