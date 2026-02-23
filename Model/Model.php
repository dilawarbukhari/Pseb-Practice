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
    public function deleteUser($id = null)
    {
        $sql = "DELETE FROM client WHERE Id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("i", $id);

        $stmt->execute();

        if ($stmt->affected_rows == 0) {
            die('User not found');
        }
        return ['message' => 'User has been delete successfully'];
    }
    public function addUser($name, $email, $phone, $address, $created)
    {
        if ($name && $email && $phone && $address && $created) {
            $search = "Select * from Client Where Email = ? ";
        $stmt = $this->conn->prepare($search);
        $stmt->bind_param('s',$email);
         $stmt->execute();
         $result = $stmt->get_result();
            // $query = $this->conn->query($search);
            if ($result->num_rows == 0) {
                $sql = "INSERT into Client (Name , Email, Phone,Address,Created) values (?,?,?,?,?)";
                $query = $this->conn->prepare($sql);  
                $query->bind_param('ssiss', $name,$email,$phone,$address,$created);
                $query->execute();
                // var_dump($query);
                // die;
                if (!($query->affected_rows > 0)) {
                    die('Data not added');
                }
                return ['message' => 'Data has been added successfully'];
            } else {
                return ['message' => 'User with this email address already exist ! Try new One'];
            }
            return ['message' => 'Data not valid'];
        }
    }
    public function updateUser($id, $name, $email, $phone, $address, $created)
    {
        if ($name && $email && $phone && $address && $created) {
            $search = "Select * from Client Where Email = '$email' ";
          $stmt = $this->conn->prepare($search);
        $stmt->bind_param('s',$email);
         $stmt->execute();
         $result = $stmt->get_result();
            // $query = $this->conn->query($search);
            if ($result->num_rows == 0) {
                $sql = "update client  Set Name='$name', Email= '$email', Phone = $phone , Address= '$address',Created='$created' " . " WHERE Id = ?";
               $query = $this->conn->prepare($sql);  
                $query->bind_param('ssissi', $name,$email,$phone,$address,$created,$id);
                $query->execute();
                // var_dump($query);
                // die;
                if (!($query->affected_rows > 0)) {
                    die('Data not updated');
                }
                return ['message' => 'Data has been updated successfully'];
            } else {
                return ['message' => 'User with this email address already exist'];
            }
        }
        return ['message' => 'Data not valid'];
    }
}
