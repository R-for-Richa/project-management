<?php
class Dbh {
    // private $host = "localhost_db";  
    // private $pass = "nera";
    private $host = "localhost";  
    private $user = "root";
    private $pass = "";
    private $dbName = "projektas";

    public function connect(){
        try{
            $dsn = "mysql:host=".$this->host.";dbname=".$this->dbName;
            $pdo = new PDO($dsn, $this->user, $this->pass);
            // $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        }catch(PDOException $error){
            return $error;
        }
    }
}
