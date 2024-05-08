<?php
class Database{
    
    // specify your own database credentials when published
    
    private $host = "127.0.0.1";
    private $db_name = "db_service";
    private $username = "root";
    private $password = "";
    public $conn;
    
    // get the database connection
    public function getConnection(){
 
        $this->conn = null;
 
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
 
        return $this->conn;
    }
}
?>