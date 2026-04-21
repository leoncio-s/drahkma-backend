<?php

namespace App\Database;

use App\Database\Databases;
use Exception;
use PDO;
use PDOException;

class MySqlDatabaseImpl extends Databases{

    protected static Databases $INSTANCE;

    public function __construct() {
        parent::__construct();
        $this->connect();
    }

    //     if(!isset(self::$instance)){
    //         $host = "host=" . DB_HOST . ";";
    //         $port = "port=" . DB_PORT;
    //         $dbName = "dbname=" . DB_DATABASE . ";";
    //         $username = DB_USERNAME;
    //         $password = DB_PASSWORD;
    //         $connectionString = "mysql:" . $dbName . $host . $port;
    //         self::$instance = self::connect($connectionString, $username, $password);
    //     }
    // }

    private function connect()
    {

        $host = "host=" . DB_HOST . ";";
        $port = "port=" . DB_PORT;
        $dbName = "dbname=" . DB_DATABASE . ";";
        $username = DB_USERNAME;
        $password = DB_PASSWORD;
        $connectionString = "mysql:" . $dbName . $host . $port . ';charset=utf8mb4';
        
        try{
            $db = new PDO($connectionString, $username, $password, [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT=>10
            ]);
    
            $this->getSqlPathFiles();
            
            return $db;
        }catch(PDOException $e){
            throw $e;
        }

        return null;
    }

    private function getSqlPathFiles(){
        $directory = __DIR__ . "/sql";
        if (file_exists($directory)){
            $files = scandir($directory);
            foreach($files as $file){
                if ($file == '.' or $file=='..') continue;
                else if(pathinfo($file)['extension'] == "sql"){
                    if(!$this->db->inTransaction())
                        $this->db->beginTransaction();
                    try{
                        $filename = $directory . '/' . $file;
                        $open = fopen($filename, 'r') or die("error to open file " . $file);
                        $read = fread($open, filesize($filename));
                    
                        $this->db->exec($read);
                        unlink($filename);
                        if($this->db->inTransaction())
                            $this->db->commit();
                    }catch(Exception $e){
                        if($this->db->inTransaction())
                            $this->db->rollBack();
                        throw $e;
                    }finally{
                        fclose($open);
                    }
                }
            }
        }
        
    }
}