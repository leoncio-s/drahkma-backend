<?php

namespace App\Database;

use Exception;
use PDO;
use PDOException;

abstract class Databases{

    protected static Databases $INSTANCE;
    protected ?PDO $db;

    public function __construct() {

        // var_dump(isset(self::$INSTANCE));

        if(!isset(Databases::$INSTANCE)){
            $this->db = $this->connect();
            self::$INSTANCE = $this;
            return self::$INSTANCE;
        }else{
            return self::$INSTANCE;
        }
        // echo __CLASS__ . "Initializated";
    }

    private function connect(){
        $host = "host=" . DB_HOST . ";";
        $port = "port=" . DB_PORT;
        $dbName = "dbname=" . DB_DATABASE . ";";
        $username = DB_USERNAME;
        $password = DB_PASSWORD;
        $connectionString = "mysql:" . $dbName . $host . $port;

        try{
            $db = new PDO($connectionString, $username, $password);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // return $pdo;
            return $db;
        }catch(PDOException $e){
            // throw $e->getCode();
            // echo($e->getMessage());
            // var_dump("DB connection Error");
            // throw $e;
            return null;
        }
        return null;
    }

    // protected function getConnection(){
    //     if(!isset(self::$INSTANCE)){
    //         return self::$INSTANCE;
    //     }else{
    //         self::$INSTANCE = $this->connect();
    //     }
    // }

    public function execute(string $sql){
        $this->db->beginTransaction();
        try{
            $ret = $this->db->exec($sql);

            $this->db->commit();

            return $ret;
        }catch(Exception $e){
            $this->db->rollBack();
            // echo($e->getMessage());

            return null;
        }
    }

    public function select(string $sql, array $parameters){

        // echo isset(Databases::$INSTANCE);
        // if(isset(Databases::$INSTANCE)) $this->connect();
        $this->db->beginTransaction();
        try{
            $ret = $this->db->prepare($sql);

            // var_dump($ret);
            $ret->execute($parameters);
            $this->db->commit();
        
            return $ret->fetchAll();
        }catch(Exception $e){
            $this->db->rollBack();
            throw $e;
        }
    }

    public function insert(string $sql, array $parameters){

        // echo isset(Databases::$INSTANCE);
        // if(isset(Databases::$INSTANCE)) $this->connect();

        $this->db->beginTransaction();
        try{
            $ret = $this->db->prepare($sql);

            // var_dump($ret);
            $ret->execute($parameters);
            $id = $this->db->lastInsertId();
            $this->db->commit();
            return $id;

        }catch(Exception $e){
            $this->db->rollBack();
            // echo($e->getMessage());

            throw $e;
        }
    }

    public static function getDBConn(){
        return self::$INSTANCE->db;
    }
}