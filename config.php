<?php




use App\Database\MySqlDatabaseImpl;

define('INI_FILE', __DIR__. '/lfinanca.ini');

$ini_file = parse_ini_file(INI_FILE, true);

define("DB_DRIVER", isset($ini_file['Database']['driver']) ? $ini_file['Database']['driver'] : "");
define("DB_HOST", isset($ini_file['Database']['host']) ? $ini_file['Database']['host'] : "");
define("DB_PORT", isset($ini_file['Database']['port']) ? $ini_file['Database']['port'] : "");
define("DB_DATABASE", isset($ini_file['Database']['database']) ? $ini_file['Database']['database'] : "");
define("DB_USERNAME", isset($ini_file['Database']['username']) ? $ini_file['Database']['username'] : "");
define("DB_PASSWORD", isset($ini_file['Database']['password']) ? $ini_file['Database']['password'] : "");
define("APP_KEY", isset($ini_file["APP"]["APP_KEY"]) ? $ini_file["APP"]["APP_KEY"] : "123hjh");
define("API_ROUTE", isset($ini_file['API']["API_ROUTER"]) ? $ini_file['API']['API_ROUTER'] : '/api/v1');

// email
define("MAIL_MAILER", isset($ini_file["EMAIL"]["MAIL_MAILER"]) ? $ini_file["EMAIL"]["MAIL_MAILER"] : ""); 
define("MAIL_HOST", isset($ini_file["EMAIL"]["MAIL_HOST"]) ? $ini_file["EMAIL"]["MAIL_HOST"] : ""); 
define("MAIL_PORT", isset($ini_file["EMAIL"]["MAIL_PORT"]) ? $ini_file["EMAIL"]["MAIL_PORT"] : ""); 
define("MAIL_USERNAME", isset($ini_file["EMAIL"]["MAIL_USERNAME"]) ? $ini_file["EMAIL"]["MAIL_USERNAME"] : ""); 
define("MAIL_PASSWORD", isset($ini_file["EMAIL"]["MAIL_PASSWORD"]) ? $ini_file["EMAIL"]["MAIL_PASSWORD"] : ""); 
define("MAIL_ENCRYPTION", isset($ini_file["EMAIL"]["MAIL_ENCRYPTION"]) ? $ini_file["EMAIL"]["MAIL_ENCRYPTION"] : ""); 
define("MAIL_FROM_ADDRESS",isset($ini_file["EMAIL"]["MAIL_FROM_ADDRESS"]) ? $ini_file["EMAIL"]["MAIL_FROM_ADDRESS"] : "");
define("MAIL_FROM_NAME",isset($ini_file["EMAIL"]["MAIL_FROM_NAME"]) ? $ini_file["EMAIL"]["MAIL_FROM_NAME"] : "");
// define("")

if(isset($_SERVER['HTTPS'])) 
    if ($_SERVER['HTTPS'] != 'off')
        define("SERVER_HOST", 'https://' . $_SERVER['HTTP_HOST']);
    else
        define("SERVER_HOST", 'http://' . $_SERVER['HTTP_HOST']);
else
    define("SERVER_HOST", 'http://' . $_SERVER['HTTP_HOST']);

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/src');

$GLOBALS["DB_CONNECTION"] = new MySqlDatabaseImpl;