<?php

    class MySQLConn{
        private $conn;
        private $table = 'paradas';

        public $cp;
        public $np;
        public $ed;
        public $py;
        public $px;

        public function __construct($db){
            $this->conn = $db;
        }


        //CRUD
        public function read(){
            $query = 'SELECT cp, np, ed, py, px FROM ' . $this->table . '';

            $statement = $this->conn->prepare($query);

            $statement->execute();
            
            return $statement;
        }

        public function create(){

            
            $create = 'CREATE DATABASE BusStops IF NOT EXISTS';

            $creating =  $this->conn->prepare($create);


            $createtable = 'CREATE TABLE IF NOT EXISTS ' . $this->table . '(
                cp = varchar(100),
                np = varchar(100),
                ed = varchar(100),
                py = varchar(100),
                px = varchar(100)
                );';
            
            $creating =  $this->conn->prepare($createtable);

            $query = 'INSERT INTO ' .$this->table .'
                SET
                    cp = :cp,
                    np = :np,
                    ed = :ed,
                    py = :py,
                    px = :px';

        
            $statement = $this->conn->prepare($query);

            $this->nome = htmlspecialchars(strip_tags($this->nome));

            $statement->bindParam(':cp', $this->cp);
            $statement->bindParam(':np', $this->np);
            $statement->bindParam(':ed', $this->ed);
            $statement->bindParam(':py', $this->py);
            $statement->bindParam(':px', $this->px);


            if($statement->execute()){
                return true;
            }

            printf("Erro: %s", $statement->error);
            return false;
        }

    }