<?php
    class Database{
        private $connection;

        function __construct(){
            $db = new Connection();
            $this->connection = $db->DBConnection();
        }

        public function select($what, $table, $where = null){
            $query = $this->connection->prepare('SELECT ' . $what . ' FROM ' . $table . ' ' . $where);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }

        public function insert($table,$column,$value){
            $sql = "INSERT INTO " . $table . " (" . $column . ") VALUES (" . $value . ")";
            $this->connection->exec($sql);
        }

        public function update($table,$data,$where){
            $useValue = "";
            foreach($data as $key => $value){
                $useValue .= $key . "='" . $value . "', ";
            }
            $useValue = substr($useValue, 0, -2);
            $sql = "UPDATE " . $table . " SET " . $useValue . " WHERE " . $where;
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
        }
    }
?>


