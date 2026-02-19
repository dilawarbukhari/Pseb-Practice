<?php
class db{
  private  $hostname = "localhost";
    private $username = "root";
    private $password = ""; 
    private $dbname = "practice";
    public $conn;
    public $Connection=false;
    public function __construct()
    {
        if(!$this->Connection){
        $this->conn = new mysqli($this->hostname, $this->username, $this->password, $this->dbname);
        if ($this->conn->connect_error) {   
        die("Connected Failed". $this->conn->connect_error );              
        } else {
            $this->Connection=true;
        }
    }
    else{
    die('Connection already enabled');
    }
}
public function __destruct()
{
   if(!$this->Connection){
die("Connected Already Closed");
   }
    $this->conn->close();
    $this->Connection=false;
}
}
?>