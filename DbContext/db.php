<?php
class db{
  private  $hostname = "sql12.freesqldatabase.com";
    private $username = "sql12817841";
    private $password = "xqyt2i67t6"; 
    private $dbname = "sql12817841";
    private $port = 3306;
    public $conn;
    public $Connection=false;
    public function __construct()
    {
        if(!$this->Connection){
        $this->conn = new mysqli($this->hostname, $this->username, $this->password, $this->dbname,$this->port);
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