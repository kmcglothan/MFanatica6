<?php
/**
 * Created by PhpStorm.
 * User: KevinM
 * Date: 10/9/17
 * Time: 1:05 PM
 */

class Database{
    // specify your own database credentials
    private $host = "paradigmusic.com";
    private $db_name = "MF6";
    private $username = "root";
    private $password = "m3m3nt0viv3r3";
    public $conn;

    //get the database connection
    public function getConnection(){

        $this->conn = null;

        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" .$this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>