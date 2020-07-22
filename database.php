<?php
class Database{
  
    // specify your own database credentials
    private $host = "localhost";
    private $db_name = "Bodega";
    private $username = "postgres";
    private $password = "root";
    private $port = "5432";
    public $conn;
  
    // get the database connection
    public function getConnection(){
  
        $this->conn = null;
  
        try{
            $servidor = "pgsql:host=".BD_SERVIDOR.";port=".BD_PUERTO.";dbname=".BD_NOMBRE_BD;
            $this->conn = new PDO("pgsql:host=" . $this->host . ";port=".$this->port.";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
  
        return $this->conn;
    }
}
?>