<?php
require './DbContext/db.php';
class Model extends db
{

    public function getAll()
    {
        $sql = 'Select * from client';
        $query = $this->conn->query($sql);

        if (!$query->num_rows > 0) {
            die("No record found");
        }
        $User[] = $query->fetch_all(MYSQLI_ASSOC);
        return $User;
    }
    public function deleteUser($id=null){
 
    $sql= "Delete from client Where Id=$id";
    $query= $this->conn->query($sql);

    if(!$query){
  die('User not found');
    }

    return ['message' =>'User has been delete successfully'];

    }
}
