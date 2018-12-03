<?php
    class Database{

        private $host = 'localhost';
        private $db = 'BusStops';
        private $user = 'root';
        private $pass = '';
        private $conn;

    public function connect(){
        $this->conn = null;

        try{
            $this->conn = new PDO('mysql:host='  . $this->host . ';dbname=' . $this->db, $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        } catch(PDOException $error){
            echo 'Falha ao conectar: ' . $error->getMessage();
        }
        return $this->conn;
    }

}

?>