<?php

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

use Symfony\Component\Dotenv\Dotenv;
use App\Kernel;

(new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');


session_start([
    'cookie_httponly' => true,
    'cookie_secure' => true,
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true,
    'cookie_lifetime' => 86400,
    'read_and_close'  => true,
    'name'=> 'DRAHKMASESS'
]);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

return function (array $context) {
    return new Kernel(
        $context['APP_ENV'],
        (bool) $context['APP_DEBUG']
    );
};
