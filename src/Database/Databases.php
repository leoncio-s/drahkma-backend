<?php

namespace App\Database;

use Exception;
use PDO;
use PDOException;

abstract class Databases{

    protected static Databases $INSTANCE;
    protected ?PDO $db;

    public function __construct(
        protected string $dbDriver,
        protected string $dbHost,
        protected int $dbPort,
        protected string $dbDatabase,
        protected string $dbUsername,
        protected string $dbPassword,
    ) {
        $this->db = $this->connect();
    }

    protected function connect(){
        $host = "host=" . $this->dbHost . ";";
        $port = "port=" . $this->dbPort;
        $dbName = "dbname=" . $this->dbDatabase . ";";
        $username = $this->dbUsername;
        $password = $this->dbPassword;
        $connectionString = $this->dbDriver . ":" . $dbName . $host . $port . ';charset=utf8mb4';

        try{
            $db = new PDO($connectionString, $username, $password, [
                // PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            ]);
            // return $pdo;
            return $db;
        }catch(PDOException $e){
            throw $e;
            // return null;
        }
        return null;
    }


    public function execute(string $sql){
        $this->db->beginTransaction();
        try{
            $ret = $this->db->exec($sql);

            $this->db->commit();

            return $ret;
        }catch(Exception $e){
            $this->db->rollBack();
            // echo($e->getMessage());

            // return null;
            throw $e;
        }
    }

    public function select(string $sql, array $parameters)
    {

        $this->db->beginTransaction();
        try{
            $ret = $this->db->prepare($sql);

            $ret->execute($parameters);
            $this->db->commit();
        
            return $ret->fetchAll();
        }catch(PDOException $e){
            $this->db->rollBack();
            throw $e;
        }
    }

    public function insert(string $sql, array $parameters)
    {


        $this->db->beginTransaction();
        try{
            $ret = $this->db->prepare($sql);

            $ret->execute($parameters);
            $id = $this->db->lastInsertId();
            $this->db->commit();
            return $id;

        }catch(Exception $e){
            $this->db->rollBack();

            throw $e;
        }
    }

    public function getDBConn(){
        return $this->db;
    }
}