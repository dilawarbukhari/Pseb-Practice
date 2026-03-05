<?php
require './jwtHandler.php';
require './DbContext/db.php';

use Firebase\JWT\JWT;

class Model extends db
{
    private $_Jwt;

    public function __construct()
    {

        parent::__construct();
        $this->_Jwt = new JwtHandler();
    }
    public function getAll($accesstoken)
    {
        $token = $this->_Jwt->decodeJwt($accesstoken);

        $Id = $token[0]->{'$userId'};
        $check = $this->checkToken($token);

        if (!$check) {
            $sql = "Select * from refresh_token Where Id = ? && isDeleted !=true";
            $query = $this->conn->prepare($sql);
            $query->bind_param("i", $Id);
            $query->execute();
            $result = $query->get_result();
            if (!$result->num_rows > 0) {
                return ["response" => "Please authenticate First"];
            }

            $row = $result->fetch_assoc();
            //   $currentTime = time();
            if ($row['expiry_date'] < time()) {
                $update = "Update refres_token  Set isDeleted= true  where Id = ?";
                $query = $this->conn->prepare($update);
                $query->bind_param("i", $Id);
                $query->execute();
                if (! $query->affected_rows > 0) {
                    return ['response' => 'request not executed'];
                }
                return ["response" => "please login again token expired"];
            }
            $function = $this->payload($Id);
            $accessToken = $function['accessToken'];
            return ['accessToken' => "$accessToken"];
        }
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
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 0) {
                $sql = "INSERT into Client (Name , Email, Phone,Address,Created) values (?,?,?,?,?)";
                $query = $this->conn->prepare($sql);
                $query->bind_param('ssiss', $name, $email, $phone, $address, $created);
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
    public function login($input)
    {
        $email = $input['email'];
        $password = $input['password'];
        if (!empty($email && $password)) {
            $sql = "Select * from signup Where email = ? ";
            $query = $this->conn->prepare($sql);
            $query->bind_param("s", $email);
            $query->execute();
            $result = $query->get_result();
            if (!$result->num_rows > 0) {
                return [
                    ["message" => 'Email not found'],
                    ['status' => '400']
                ];
            }
            $row = $result->fetch_assoc();
            $hashpassword = $row['Password'];
            $userId = $row['Id'];
            if (password_verify($password, $hashpassword)) {
                $function = $this->payload($userId);
                $accessToken = $function['accessToken'];
                $refreshToken = $function['refreshToken'];
                //  $this->_Jwt->decodeJwt($accessToken);
                if (empty($refreshToken)) {
                    return [
                        ['message' => 'Token not generated'],
                        ['status' => '400']
                    ];
                }
                $refreshexpiry = date('Y-m-d H:i:s', time() + (7 * 24 * 60 * 60));
                $createdAt = date('Y-m-d H:i:s');
                $sql = "Insert into refresh_token (Id,refresh_token,expiry_date,created_at) Values (?,?,?,?)";
                $query = $this->conn->prepare($sql);
                $query->bind_param("isss", $userId, $refreshToken, $refreshexpiry, $createdAt);
                $query->execute();
                if (!$query->affected_rows > 0) {
                    return [
                        ['message' => 'refresh token not added'],
                        ['status' => '400']
                    ];
                }
                return [
                    [
                        'message' => 'Data has been added successfully'
                    ],
                    ['status' => '200'],
                    [
                        'data' => [['accessToken' => "$accessToken"], ['refreshToken' => "$refreshToken"]]
                    ]
                ];
            } else {
                return [
                    ['message' => "Please enter the correct password"],
                    ['status' => '400']
                ];
            }
        }
        return [
            ['message' => 'Enter both username and password'],
            ['status' => '400']
        ];
    }
    public function registerUser($input)
    {
        $firstname = $input['firstname'];
        $lastName = $input['lastname'];
        $email = $input['email'];
        $password = $input['password'];
        $checkpassword = $input['confirmPassword'];
        $hashpassowrd = password_hash($password, PASSWORD_DEFAULT);
        if (!empty($firstname && $lastName && $email && $password)) {
            $sql = "SELECT * FROM signup WHERE email = ?";
            $query = $this->conn->prepare($sql);
            $query->bind_param("s", $email);
            $query->execute();
            $query->store_result();
            if ($query->num_rows > 0) {
               return [['message' => 'Email already exist'],  ['status' => '400']];
            }
                if ($password !== $checkpassword) {
                    
                return [['message' => 'Password not match'],
                    ['status' => '400']];
                }
                    if (strlen($password) < 8) {

                    return [['message' => 'Password must be at least 8 characters long'],
                    ['status' => '400']];
                
                    }
                        if (!preg_match('/[A-Z]/', $password))
                            return [['message' => 'Password must contain uppercase'],
                    ['status' => '400']];
                        if (!preg_match('/[a-z]/', $password))
                            return [['message' => 'Password must contain lowercase'],
                    ['status' => '400']];
                        if (!preg_match('/[0-9]/', $password))
                            return [['message' => 'Password must contain number'],
                    ['status' => '400']];
                        if (!preg_match('/[\W_]/', $password))
                            return [['message' => 'Password must contain special char'],
                    ['status' => '400']];
                        $sql = "INSERT INTO signup (Firstname,Lastname,Email,Password) VALUES (?,?,?,?)";
                        $query = $this->conn->prepare($sql);
                        $query->bind_param("ssss", $firstname, $lastName, $email, $hashpassowrd);
                        $query->execute();
                       
                        if (!$query->affected_rows > 0) {
                    return [['message' => 'Data not added successfully'],
                    ['status' => '400']];  
                        }
                            $userId = $this->conn->insert_id;                 
                            $roleId = 1;
                            $sqlRole = "INSERT INTO user_roles (id, role_Id) VALUES (?, ?)";
                            $queryRole = $this->conn->prepare($sqlRole);
                            $queryRole->bind_param("ii", $userId, $roleId);
                            $queryRole->execute();
                            if($query->affected_rows > 0){
                             return  [['message' => 'Data has been added successfully'],
                    ['status' => '200']]
                                ;
                            }
                                  return [['message' => 'Data not added in user_roles'],
                    ['status' => '400']];
                                          
                       
                    
            // }
            // 
        }
        return [['message' => 'All Fields are required'],  ['status' => '400']];
    }
    public function updateUser($id, $name, $email, $phone, $address, $created)
    {
        if ($name && $email && $phone && $address && $created) {
            $search = "Select * from Client Where Email = '$email' ";
            $stmt = $this->conn->prepare($search);
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            // $query = $this->conn->query($search);
            if ($result->num_rows == 0) {
                $sql = "update client  Set Name='$name', Email= '$email', Phone = $phone , Address= '$address',Created='$created' " . " WHERE Id = ?";
                $query = $this->conn->prepare($sql);
                $query->bind_param('ssissi', $name, $email, $phone, $address, $created, $id);
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
    private function checkToken($token)
    {
        $expiry_date = $token[0]->exp;
        $currentTime = time();
        if ($expiry_date < $currentTime) {
            return false;
        } else {
            return true;
        }
    }
    private function payload($Id)
    {
        $sql = "Select * from user_roles where id = ?";
        $query = $this->conn->prepare($sql);
        $query->bind_param('i', $Id);
        $query->execute();
        $result = $query->get_result();

        if (!$result->num_rows > 0) {
            return ["response" => "role not found"];
        }
        $row = $result->fetch_assoc();
        $roleId = $row['role_Id'];
        $sql = "
       SELECT p.permission_Id, p.permission_name
        FROM role_permission rp
        Left Join permission p ON rp.permission_Id = p.permission_Id
        WHERE rp.role_Id = ?
";
        $query = $this->conn->prepare($sql);
        $query->bind_param("i", $roleId);
        $query->execute();
        $result = $query->get_result();
        if (!$result->num_rows > 0) {
            return ["response" => "Permission not found"];
        }
        $permissions = [];
        while ($row = $result->fetch_assoc()) {
            $permissions[] = $row;
        }


        $accessToken =  $this->_Jwt->generateaccessToken($permissions, $Id);

        $refreshToken =  $this->_Jwt->generaterefreshToken($permissions, $Id);
        return [
            'accessToken'  => $accessToken,
            'refreshToken' => $refreshToken
        ];
    }
}
