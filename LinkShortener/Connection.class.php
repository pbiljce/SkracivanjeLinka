<?php
define('DB_SERVER','localhost');
define('DB_USER','root');
define('DB_PASSWORD','');
define('DB_DATABASE','LinkShortener');
class Connection{
    private $connection;

    function __construct(){
        $this->connection = $this->connect();
    }

    private function connect(){
        try {
            // require_once 'config.php';
            $conn = new PDO('mysql:host=' . DB_SERVER . ';dbname=' . DB_DATABASE, DB_USER, DB_PASSWORD);
            $conn -> exec("set names utf8");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
        catch(PDOException $e)
            {
            echo "Neuspješna konekcija: " . $e->getMessage();
            }
        return $conn;
    }
    public function DBConnection(){
        return $this->connection;
    }
}
?>