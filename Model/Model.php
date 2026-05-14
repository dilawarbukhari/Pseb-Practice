<?php
require './jwtHandler.php';
require './DbContext/db.php';
require './EmailServer.php';
require './vendor/autoload.php';
// require './config.php';
require './Configuration.php';

use Firebase\JWT\JWT;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Api\Admin\AdminApi;

class Model extends db
{
    private $_Jwt;
    private $emailServer;
    public $userId = 0;

    public function __construct()
    {

        parent::__construct();
        $this->_Jwt = new JwtHandler();
        $this->emailServer = new Config();
    }

    public function deletePermission($input)
    {
        if (!$this->checkToken($this-> getBearerToken())) {
            return [['message' => 'Please login again'],  ["status" => "400"]];
        }
        $userId = $this->getUserId($this-> getBearerToken());
        if (!$this->hasPermission($userId, "delete_permission")) {
            return [
                ["message" => "you have no permission to do the process"],
                ["status" => "400"]
            ];
        }
        $id = $input["permission_Id"];
        $update_at = date('Y-m-d H:i:s', time());
        $sql = "Update permission Set IsDeleted=true, updated_by=?, updated_at=?   WHERE permission_Id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("isi", $userId, $update_at, $id);

        $stmt->execute();

        if ($stmt->affected_rows == 0) {
            return [
                ['message' => 'permission not deleted'],
                ["status" => '400']
            ];
        }
        return [
            ['message' => 'permission has been deleted successfully'],
            ["status" => '200']
        ];
    }

  public  function updatePermission($input)
    {
        if (!$this->checkToken($this-> getBearerToken())) {
            return [['message' => 'Please login again'],  ["status" => "400"]];
        }
        $userId = (!empty($this-> getBearerToken())) ? $this->getUserId($this-> getBearerToken()) : null;
        if (!$this->hasPermission($userId, "update_permission")) {
            return [
                ["message" => "you have no permission to do the process"],
                ["status" => "400"]
            ];
        }
        $name = $input['permission_name'];
        $permission_Id = $input['permission_Id'];
        $updated_at = date('Y-m-d H:i:s', time());
        $sql = "UPDATE permission SET permission_name = ?, updated_at = ?, updated_by = ? WHERE permission_Id = ?";
        $query = $this->conn->prepare($sql);
        $query->bind_param('ssii', $name, $updated_at, $userId, $permission_Id);
        $query->execute();

        if (!$query->affected_rows > 0) {
            return [
                ['message' => 'Data not Successfully added'],
                ["status" => "400"]
            ];
        }
        return [['message' => 'Data Successfully updated'], ["status" => "200"]];
    }
      public function addPermission($input)
    {
        if (!$this->checkToken($this-> getBearerToken())) {
            return [['message' => 'Please login again'],  ["status" => "400"]];
        }
        $userId = (!empty($this-> getBearerToken())) ? $this->getUserId($this-> getBearerToken()) : null;
        if (!$this->hasPermission($userId, "add_permission")) {
            return [
                ["message" => "you have no permission to do the process"],
                ["status" => "400"]
            ];
        }
        $name = $input['permission_name'];
        $created_at = date('Y-m-d H:i:s', time());
        $sql = "INSERT into permission (permission_name,created_by,created_at) values (?,?,?)";
        $query = $this->conn->prepare($sql);
        $query->bind_param('sis', $name, $userId, $created_at);
        $query->execute();

        if (!$query->affected_rows > 0) {
            return [
                ['message' => 'Data not Successfully added'],
                ["status" => "400"]
            ];
        }
        return [['message' => 'Data Successfully added'], ["status" => "200"]];
    }
    public function updateRolePermission($input)
    {
        if (!$this->checkToken($this-> getBearerToken())) {
            return [['message' => 'Please login again'],  ["status" => "400"]];
        }
        $userId = $this->getUserId($this-> getBearerToken());
        if (!$this->hasPermission($userId, "update_role_permission")) {
            return [
                ["message" => "you have no permission to do the process"],
                ["status" => "400"]
            ];
        }
        $roleId = $input['role_Id'];
        $sql = "SELECT permission_Id, status FROM role_permission WHERE role_Id = ? AND isDeleted != true";
        $query = $this->conn->prepare($sql);
        $query->bind_param("i", $roleId);
        $query->execute();
        $result = $query->get_result();
        $existingPermissions = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $permId = $row['permission_Id'];
                $existingPermissions[] = $permId;
                $existingStatus[$permId] = $row['status'];
            }
        }
        $newpermission = $input['permission_Id'];
        $permissionsToAdd = array_diff($newpermission, $existingPermissions);
        $permissionsToRemove = array_diff($existingPermissions,  $newpermission);

        foreach ($permissionsToAdd as $permissionId) {
            $sql = "INSERT INTO role_permission (role_Id, permission_Id, created_by, created_at) VALUES (?, ?, ?, ?)";
            $query = $this->conn->prepare($sql);
            $created_at = date('Y-m-d H:i:s', time());
            $query->bind_param("iiis", $roleId, $permissionId, $userId, $created_at);
            $query->execute();
        }
        foreach ($newpermission as $permId) {
            if (isset($existingStatus[$permId]) && $existingStatus[$permId] == true) {
                $updated_at = date('Y-m-d H:i:s', time());
                $sql = "UPDATE role_permission 
                    SET status = false, updated_by = ?, updated_at = ?
                    WHERE role_Id = ? AND permission_Id = ?";

                $query = $this->conn->prepare($sql);
                $query->bind_param("isii", $userId, $updated_at, $roleId, $permId);
                $query->execute();
            }
        }
        foreach ($permissionsToRemove as $permId) {
            if ($existingStatus[$permId] == false) {
                $updated_at = date('Y-m-d H:i:s', time());
                $sql = "UPDATE role_permission 
                    SET status = true, updated_by = ?, updated_at = ?
                    WHERE role_Id = ? AND permission_Id = ?";
                $query = $this->conn->prepare($sql);
                $query->bind_param("isii", $userId, $updated_at, $roleId, $permId);
                $query->execute();
            }
        }
        return [
            ["message" => "Role permissions updated successfully"],
            ["status" => "200"]
        ];
    }
    public function getRolePermission($input)
    {
        if (!$this->checkToken($this-> getBearerToken())) {
            return [['message' => 'Please login again'],  ["status" => "400"]];
        }
        $userId = $this->getUserId($this-> getBearerToken());
        if (!$this->hasPermission($userId, "view_role_permission")) {
            return [
                ["message" => "you have no permission to do the process"],
                ["status" => "400"]
            ];
        }
        $sql = 'Select rp.role_Id,rp.Permission_Id,p.Permission_name from role_permission rp
        Join permission p on rp.Permission_Id = p.Permission_Id
        Where role_Id = ? && rp.isDeleted !=true && p.isDeleted != true  && rp.status !=true';

        $query = $this->conn->prepare($sql);
        $query->bind_param("i", $input['role_Id']);
        $query->execute();

        $result = $query->get_result();
        if (!$result->num_rows > 0) {
            return ["message " => 'No rows effected'];
        }
        $User[] = $result->fetch_all(MYSQLI_ASSOC);
        return $User;
    }
    public function getAllPermission($input)
    {
        if (!$this->checkToken($this-> getBearerToken())) {
            return [['message' => 'Please login again'],  ["status" => "400"]];
        }
        $userId = $this->getUserId($this->getBearerToken());
        if (!$this->hasPermission($userId, "view_permission")) {
            return [
                ["message" => "you have no permission to do the process"],
                ["status" => "400"]
            ];
        }
        $sql = 'Select * from permission Where isDeleted !=true';

        $query = $this->conn->prepare($sql);
        $query->execute();
        $result = $query->get_result();

        if (!$result->num_rows > 0) {
            return ["message " => 'No rows effected'];
        }
        $User[] = $result->fetch_all(MYSQLI_ASSOC);
        return $User;
    }
    public  function updateRole($input)
    {
        if (!$this->checkToken($this-> getBearerToken())) {
            return [['message' => 'Please login again'],  ["status" => "400"]];
        }
        $userId = (!empty($this-> getBearerToken())) ? $this->getUserId($this-> getBearerToken()) : null;
        if (!$this->hasPermission($userId, "update_role")) {
            return [
                ["message" => "you have no permission to do the process"],
                ["status" => "400"]
            ];
        }
        $name = $input['role_name'];
        $role_Id = $input['role_Id'];
        $updated_at = date('Y-m-d H:i:s', time());
        $sql = "UPDATE role SET role_name = ?, updated_at = ?, updated_by = ? WHERE role_Id = ?";
        $query = $this->conn->prepare($sql);
        $query->bind_param('ssii', $name, $updated_at, $userId, $role_Id);
        $query->execute();

        if (!$query->affected_rows > 0) {
            return [
                ['message' => 'Data not Successfully added'],
                ["status" => "400"]
            ];
        }
        return [['message' => 'Data Successfully updated'], ["status" => "200"]];
    }

    public function deleteRole($input)
    {
        if (!$this->checkToken($this-> getBearerToken())) {
            return [['message' => 'Please login again'],  ["status" => "400"]];
        }
        $userId = $this->getUserId($this-> getBearerToken());
        if (!$this->hasPermission($userId, "delete_role")) {
            return [
                ["message" => "you have no permission to do the process"],
                ["status" => "400"]
            ];
        }
        $id = $input["role_Id"];
        $update_at = date('Y-m-d H:i:s', time());
        $sql = "Update role Set IsDeleted=true, updated_by=?, updated_at=?   WHERE role_Id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("isi", $userId, $update_at, $id);

        $stmt->execute();

        if ($stmt->affected_rows == 0) {
            return [
                ['message' => 'role not deleted'],
                ["status" => '400']
            ];
        }
        return [
            ['message' => 'role has been deleted successfully'],
            ["status" => '200']
        ];
    }

    public function addRole($input)
    {
        if (!$this->checkToken($this-> getBearerToken())) {
            return [['message' => 'Please login again'],  ["status" => "400"]];
        }
        $userId = (!empty($this-> getBearerToken())) ? $this->getUserId($this-> getBearerToken()) : null;
        if (!$this->hasPermission($userId, "create_role")) {
            return [
                ["message" => "you have no permission to do the process"],
                ["status" => "400"]
            ];
        }
        $name = $input['role_name'];
        $created_at = date('Y-m-d H:i:s', time());
        $sql = "INSERT into role (role_name,created_by,created_at) values (?,?,?)";
        $query = $this->conn->prepare($sql);
        $query->bind_param('sis', $name, $userId, $created_at);
        $query->execute();

        if (!$query->affected_rows > 0) {
            return [
                ['message' => 'Data not Successfully added'],
                ["status" => "400"]
            ];
        }
        return [['message' => 'Data Successfully added'], ["status" => "200"]];
    }

    public function getUserDetail($input)
    {
        if (!$this->checkToken($this-> getBearerToken())) {
            return [['message' => 'Please login again'],  ["status" => "400"]];
        }

       
        $userId = $input['user_Id'];
    //  if (!$this->hasPermission($userId, "view_user")) {
    //         return [
    //             ["message" => "you have no permission to do the process"],
    //             ["status" => "400"]
    //         ];
    //     }

        $sql = 'Select s.Id, s.firstname, s.lastname, s.email, s.role_Id,r.role_name from signup s LEFT JOIN role r ON s.role_Id = r.role_Id Where Id = ? AND s.isDeleted != true And r.IsDeleted != true' ;

        $query = $this->conn->prepare($sql);
        $query->bind_param("i", $userId);
        $query->execute();
        $result = $query->get_result();

        if (!$result->num_rows > 0) {
            return ["message " => 'No rows effected'];
        }
        $User[] = $result->fetch_all(MYSQLI_ASSOC);
        return $User;
    }
    public function getAllCategory($input)
    {
        if (!$this->checkToken($this-> getBearerToken())) {
            return [['message' => 'Please login again'],  ["status" => "400"]];
        }
        $userId = $this->getUserId($this-> getBearerToken());
        // if (!$this->hasPermission($userId, "view_category")) {
        //     return [
        //         ["message" => "you have no permission to do the process"],
        //         ["status" => "400"]
        //     ];
        // }
        $sql = 'Select * from categories   Where isDeleted !=true';

        $query = $this->conn->prepare($sql);
        $query->execute();
        $result = $query->get_result();

        if (!$result->num_rows > 0) {
            return ["message " => 'No rows effected'];
        }
        $User[] = $result->fetch_all(MYSQLI_ASSOC);
        return $User;
    }


    public function updateUserDetail($input)
    {
        if (!$this->checkToken($this-> getBearerToken())) {
            return [['message' => 'Please login again'],  ["status" => "400"]];
        }
        $userId = $this->getUserId($this-> getBearerToken());
    //  if (!$this->hasPermission($userId, "update_user")) {
    //         return [
    //             ["message" => "you have no permission to do the process"],
    //             ["status" => "400"]
    //         ];
    //     }

        $firstname = $input['firstname'];
        $lastName = $input['lastname'];
        $email = $input['email'];
        $Id = $input['user_Id'];
        $updated_at = date('Y-m-d H:i:s', time());
        $updated_by = (!empty($this-> getBearerToken())) ? $this->getUserId($this-> getBearerToken()) : null;
        $sql = "SELECT email FROM signup WHERE  Id = ? AND isDeleted != true";
        $query = $this->conn->prepare($sql);
        $query->bind_param("i", $Id);
        $query->execute();
        $result = $query->get_result();
        $CurrentUser= $result->fetch_assoc();

        $currentEmail= $CurrentUser['email'];
   
     if( $currentEmail !== $email){
        $sql = "SELECT email FROM signup WHERE  email = ? AND isDeleted != true";
        $query = $this->conn->prepare($sql);
        $query->bind_param("s", $email);
        $query->execute();
        $result = $query->get_result();

        if($result->num_rows > 0){
            return [['message' => 'Email already exist'], ['status' => '400']];
        }
     }
     $sql = "UPDATE signup 
        SET Firstname = ?, Lastname = ?, Email = ?, updated_by = ?, updated_at = ?
        WHERE Id = ?";

        $sql = "update signup Set Firstname=?,Lastname=?,Email=? , updated_by= ?  , updated_at=?  Where  Id=? ";
        $query = $this->conn->prepare($sql);
        $query->bind_param('sssisi', $firstname, $lastName, $email, $updated_by, $updated_at, $Id);
        $query->execute();
        // var_dump($query);
        // die;
        if (!($query->affected_rows > 0)) {
            return [
                ['message' => 'Data not updated successfully'],
                ["status" => "400"]
            ];
        }
        return [
            ['message' => 'Data has been updated successfully'],
            ["status" => "200"]
        ];
    }
    public function getAllProduct($input)
    {
        if (!$this->checkToken($this-> getBearerToken())) {
            return [['message' => 'Please login again'],  ["status" => "400"]];
        }
        $userId = $this->getUserId($this-> getBearerToken());
        // if (!$this->hasPermission($userId, "view_product")) {
        //     return [
        //         ["message" => "you have no permission to do the process"],
        //         ["status" => "400"]
        //     ];
        // }
        $sql = 'Select p.product_Id ,p.description,P.image, p.product_name,p.category_id,c.category_name,p.price,p.oldPrice,p.quantity  from products p Join categories c On c.category_id = p.category_id   Where p.isDeleted !=true AND c.isDeleted != true;';

        $query = $this->conn->prepare($sql);
        $query->execute();
        $result = $query->get_result();

        if (!$result->num_rows > 0) {
            return ["message " => 'No rows effected'];
        }
        $User[] = $result->fetch_all(MYSQLI_ASSOC);
        return $User;
    }

    public function getBuyerProduct($input)
    {
        if (!$this->checkToken($this-> getBearerToken())) {
            return [['message' => 'Please login again'],  ["status" => "400"]];
        }
        $userId = $this->getUserId($this-> getBearerToken());
        // if (!$this->hasPermission($userId, "view_product")) {
        //     return [
        //         ["message" => "you have no permission to do the process"],
        //         ["status" => "400"]
        //     ];
        // }
        $sql = 'Select ps.product_Id,ps.user_Id,p.description,P.image, p.product_name,p.category_id,c.category_name,p.price,p.oldPrice,p.quantity from product_users ps Join products p On p.product_Id= ps.product_Id Join categories c On c.category_id = p.category_id where ps.user_Id=? And ps.isDeleted = false AND p.isDeleted !=true AND c.isDeleted != true;';

        $query = $this->conn->prepare($sql);
        $query->bind_param("i", $userId);
        $query->execute();
        $result = $query->get_result();

        if (!$result->num_rows > 0) {
            return ["message " => 'No rows effected'];
        }
        $User[] = $result->fetch_all(MYSQLI_ASSOC);
        return $User;
    }
    public function deleteCategory($input)
    {
        if (!$this->checkToken($this-> getBearerToken())) {
            return [['message' => 'Please login again'],  ["status" => "400"]];
        }
        $userId = $this->getUserId($this-> getBearerToken());
        if (!$this->hasPermission($userId, "category_delete")) {
            return [
                ["message" => "you have no permission to do the process"],
                ["status" => "400"]
            ];
        }
        $id = $input["category_id"];
        $update_at = date('Y-m-d H:i:s', time());
        $sql = "Update categories Set IsDeleted=true, updated_by=?, updated_at=?  WHERE category_id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("isi", $userId, $update_at, $id);

        $stmt->execute();

        if ($stmt->affected_rows == 0) {
            return [
                ['message' => 'User not deleted'],
                ["status" => '400']
            ];
        }
        return [
            ['message' => 'User has been delete successfully'],
            ["status" => '200']
        ];
    }

    public function deleteProduct($input)
    {
        if (!$this->checkToken($this-> getBearerToken())) {
            return [['message' => 'Please login again'],  ["status" => "400"]];
        }
        $userId = $this->getUserId($this-> getBearerToken());
        if (!$this->hasPermission($userId, "delete_product")) {
            return [
                ["message" => "you have no permission to do the process"],
                ["status" => "400"]
            ];
        }
        $id = $input["product_Id"];
        $update_at = date('Y-m-d H:i:s', time());
        $sql = "Update products Set IsDeleted=true, updated_by=?, updated_at=?   WHERE product_Id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("isi", $userId, $update_at, $id);

        $stmt->execute();

        if ($stmt->affected_rows == 0) {
            return [
                ['message' => 'User not deleted'],
                ["status" => '400']
            ];
        }
        return [
            ['message' => 'User has been delete successfully'],
            ["status" => '200']
        ];
    }
    public function addCategory($input)
    {
        if (!$this->checkToken($this-> getBearerToken())) {
            return [['message' => 'Please login again'],  ["status" => "400"]];
        }

        $name = $input['category_name'];
        $userId = $this->getUserId($this-> getBearerToken());
        if (!$this->hasPermission($userId, "category_create")) {
            return [
                ["message" => "you have no permission to do the process"],
                ["status" => "400"]
            ];
        }
        $created_at = date('Y-m-d H:i:s', time());
        $sql = "INSERT into categories (category_name,created_by,created_at) values (?,?,?)";
        $query = $this->conn->prepare($sql);
        $query->bind_param('sis', $name, $userId, $created_at);
        $query->execute();

        if (!$query->affected_rows > 0) {
            return [
                ['message' => 'Data not Successfully added'],
                ["status" => "400"]
            ];
        }
        return [['message' => 'Data Successfully added'], ["status" => "200"]];
    }

    public function getAllroles($input)
    {

        if (!$this->checkToken($this-> getBearerToken())) {
            return [['message' => 'Please login again'],  ["status" => "400"]];
        }
        $userId = $this->getUserId($this-> getBearerToken());
        if (!$this->hasPermission($userId, "view_role")) {
            return [
                ["message" => "you have no permission to do the process"],
                ["status" => "400"]
            ];
        }
        $sql = 'Select * from role  Where isDeleted !=true';

        $query = $this->conn->prepare($sql);
        $query->execute();
        $result = $query->get_result();

        if (!$result->num_rows > 0) {
            return ["message " => 'No rows effected'];
        }
        $User[] = $result->fetch_all(MYSQLI_ASSOC);
        return $User;
    }
    public function getAllUser($input)
    {
        if (!$this->checkToken($this-> getBearerToken())) {
            return [['message' => 'Please login again'],  ["status" => "400"]];
        }
        $userId = $this->getUserId($this-> getBearerToken());
        if (!$this->hasPermission($userId, "view_user")) {
            return [
                ["message" => "you have no permission to do the process"],
                ["status" => "400"]
            ];
        }
        $sql = 'SELECT
        s.Id, 
    s.firstname,
    s.lastname,
    s.email,
    s.role_Id ,
    r.role_name
FROM signup s
JOIN role r ON s.role_Id = r.role_Id
WHERE s.isDeleted != true
  AND r.isDeleted != true
  AND s.Id != ?';
        $query = $this->conn->prepare($sql);
        $query->bind_param("i", $userId);
        $query->execute();
        $result = $query->get_result();
        if (!$result->num_rows > 0) {
            return ["message " => 'No rows effected'];
        }
        $User[] = $result->fetch_all(MYSQLI_ASSOC);
        return $User;
    }
public function addProduct($input, $file = null)
{

    if (!$this->checkToken($this->getBearerToken())) {
        return [
            ['message' => 'Please login again'],
            ["status" => "400"]
        ];
    }

    $userId = $this->getUserId($this->getBearerToken());

    if (!$this->hasPermission($userId, "create_product")) {
        return [
            ["message" => "you have no permission to do the process"],
            ["status" => "400"]
        ];
    }
    $name = $input['product_name'];
    $categoryid = $input['category_id'];
    $price = $input['price'];
    $quantity = $input['quantity'];
    $description= $input['description'];
    $oldPrice=$input['oldPrice'];

    $imagePath = null;
    $imagePublicId = null;

    if (!empty($_FILES['image']['tmp_name'])) {

        try {

            $uploadResult = (new UploadApi())->upload(
            $_FILES['image']['tmp_name'],
            ["folder" => "products"]
            );["folder" => "products"];

            $imagePath = $uploadResult['secure_url'];
            $imagePublicId = $uploadResult['public_id'];
    // var_dump($imagePublicId);
    //         die;
          

        } catch (Exception $e) {
            return [
                ['message' => 'Image upload failed: ' . $e->getMessage()],
                ["status" => "400"]
            ];
        }
    }

    $created_at = date('Y-m-d H:i:s');


    $sql = "INSERT INTO products 
            (product_name,seller_Id, Image,image_Id, category_id, price, oldPrice, quantity,Description, created_by, created_at) 
            VALUES (?,?, ?, ?, ?, ?, ?, ?,?,?,?)";

    $query = $this->conn->prepare($sql);

    $query->bind_param(
        'sissiiiisis',
        $name,
        $userId,
        $imagePath,
        $imagePublicId,
        $categoryid,
        $price,
        $oldPrice   ,
        $quantity,
        $description,
        $userId,
        $created_at
    );

    $query->execute();

    if ($query->affected_rows <= 0) {
        return [
            ['message' => 'Data not successfully added'],
            ["status" => "400"]
        ];
    }
    $productId = $this->conn->insert_id;
    $sql2 = "INSERT INTO product_users (product_Id, user_Id,created_at,created_by) VALUES (?, ?,?,?)";
        $query2 = $this->conn->prepare($sql2);

        $query2->bind_param('iisi', $productId, $userId, $created_at, $userId);
        $query2->execute();

        if ($query2->affected_rows <= 0) {
            throw new Exception("Product user mapping failed");
        }

    return [
        [
            'message' => 'Data successfully added',
            // 'image_url' => $imagePath,
            // 'image_id' => $imagePublicId
        ],
        ["status" => "200"]
    ];
}

    public function login($input)
{
    $email = $input['email'];
    $password = $input['password'];
    if (empty($email) || empty($password)) {
        return [
            ['message' => 'Enter both username and password'],
            ['status' => '400']
        ];
    }

    $sql = "SELECT * FROM signup WHERE email = ? AND isDeleted != true";
    $query = $this->conn->prepare($sql);
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows <= 0) {
        return [
            ["message" => 'Email not found'],
            ['status' => '400']
        ];
    }
 
    $row = $result->fetch_assoc();

    $IsChanged = $row['isChanged'];
    $IsEmailVerified= $row['IsEmailVerified'];

 $IsTerminated = $row['IsTerminated'];
 
 if($IsTerminated !== 0){
  return [
                ["message" => 'Please contact Administration to reset your password'],
                ['status' => '400']
            ];
 }
    if ($row['isChanged'] != true) {

        $lastLogin = strtotime($row['last_login']);

        // echo $lastLogin;
        // echo (time() - (15 * 24 * 60 * 60));
        // die;

        if ($lastLogin < time() - (15 * 24 * 60 * 60)) {

            $sql = "UPDATE signup SET isTerminated = true WHERE email = ? AND isDeleted != true";
            $query = $this->conn->prepare($sql);
            $query->bind_param("s", $email);
            $query->execute();

            return [
                ["message" => 'Please contact Administration to reset your password'],
                ['status' => '400']
            ];
        }
    }

    $hashpassword = $row['Password'];
    $userId = $row['Id'];


    if (!password_verify($password, $hashpassword)) {
        return [
            ['message' => "Please enter the correct password"],
            ['status' => '400']
        ];
    }

    $accessToken = $this->_Jwt->generateaccessToken($userId,$IsChanged, $IsEmailVerified);

    $lastLogin = date('Y-m-d H:i:s');
    $sql = "UPDATE signup SET last_login = ? WHERE email = ?";
    $query = $this->conn->prepare($sql);
    $query->bind_param("ss", $lastLogin, $email);
    $query->execute();

    return [
        ['message' => 'Login successfully'],
        ['status' => '200'],
        [
            'data' => [
                ['accessToken' => $accessToken]
                // ['isChanged' => $IsChanged]
            ]
        ]
    ];
}
public function ResendforgotOTP($input){
$email = $input['email'];
 $user = $this->getUser($email);
 $user_Id= $user['Id'];
 $otp = rand(10000,99999);
 $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));  
$sql = "Update user_otps Set forgotPasswordOtp=? ,fotgotPasswordExpiry=?  where user_Id= ? ";
$query= $this->conn->prepare($sql);
$query->bind_param('isi',$otp,$expiry,$user_Id);
$query->execute();
if($query->affected_rows>0){

$get= $this->getEmail($user_Id);
$email= $get['email'];


$this->emailServer->sendOTP($email,$otp); 
 return [
        ['message' => 'OTP has been resend on your email address'],
        ['status' => '200'],
 ];
}
}
public function ResendOTP($input){
 $user_Id= $input['user_Id'];
 $otp = rand(10000,99999);
 $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));  
$sql = "Update user_otps Set otp=? ,expiry=?  where user_Id= ? ";
$query= $this->conn->prepare($sql);
$query->bind_param('ssi',$otp,$expiry,$user_Id);
$query->execute();
if($query->affected_rows>0){

$get= $this->getEmail($user_Id);
$email= $get['email'];


$this->emailServer->sendOTP($email,$otp); 
 return [
        ['message' => 'OTP has been resend on your email address'],
        ['status' => '200'],
 ];
}
$sql= 'Insert into user_otps (user_Id, otp,expiry) Values (?,?,?)';
$query= $this->conn->prepare($sql);
$query->bind_param('iss', $user_Id,$otp,$expiry);
$query->execute();
if($query->affected_rows==0){
    return [
        ['message' => 'Error in OTP generation'],
        ['status' => '200'],
 ];
}
$get= $this->getEmail($user_Id);
$email= $get['email'];
$this->emailServer->sendOTP($email,$otp); 
 return [
        ['message' => 'OTP has been sent on your email address'],
        ['status' => '200'],
 ];
}
 public function ForgotPassword($input)
    {    
         $email= $input['email'];
       
        $password = $input['newPassword'];
        $checkpassword = $input['confirmPassword'];
        $updated_at = date('Y-m-d H:i:s', time());
        $updated_by = !empty($this-> getBearerToken())
            ? $this->getUserId($this-> getBearerToken())
            : 0;
        $hashpassword = password_hash($password, PASSWORD_DEFAULT);
        if ($password !== $checkpassword) {
            return [
                ['message' => 'Password not match'],
                ['status' => '400']
            ];
        }
        if (strlen($password) < 8) {

            return [
                ['message' => 'Password must be at least 8 characters long'],
                ['status' => '400']
            ];
        }
        if (!preg_match('/[A-Z]/', $password))
            return [
                ['message' => 'Password must contain uppercase'],
                ['status' => '400']
            ];
        if (!preg_match('/[a-z]/', $password))
            return [
                ['message' => 'Password must contain lowercase'],
                ['status' => '400']
            ];
        if (!preg_match('/[0-9]/', $password))
            return [
                ['message' => 'Password must contain number'],
                ['status' => '400']
            ];
        if (!preg_match('/[\W_]/', $password))
            return [
                ['message' => 'Password must contain special char'],
                ['status' => '400']
            ];
        $sql = "SELECT email, Password FROM signup WHERE email = ?";
        $query = $this->conn->prepare($sql);
        $query->bind_param("s", $email);
        $query->execute();
        $result = $query->get_result();
        $row = $result->fetch_assoc();
        $oldemail = $row['email'];
        $oldPassword=$row['Password'];
        if (strtolower($oldemail) !== strtolower($email)){
            return [
                ['message' => 'Email not found'],
                ['status' => '400']
            ];
         }
      if(password_verify($password, $oldPassword)){
            return [
                ['message' => 'New password cannot be the same as the old password'],
                ['status' => '400']
            ];
        }
        $sql = "UPDATE signup SET Password = ? , updated_at = ?, updated_by = ?
         WHERE email = ?";
        $query = $this->conn->prepare($sql);
        $query->bind_param("ssis", $hashpassword, $updated_at, $userId, $email);
        $query->execute();

        if (!$query->affected_rows > 0) {
            return [
                ['message' => 'Password updated failed'],
                ['status' => '200']
            ];
        }      
             return [
                ['message' => 'Password update Successfully'],
                ['status' => '200']
            ];       
    }
    public function changePassword($input)
    {    
         $email= $input['email'];
        $oldPassword = $input['oldPassword'];
        $password = $input['newPassword'];
        $checkpassword = $input['confirmPassword'];
        $updated_at = date('Y-m-d H:i:s', time());
        $updated_by = !empty($this-> getBearerToken())
            ? $this->getUserId($this-> getBearerToken())
            : 0;
        $hashpassword = password_hash($password, PASSWORD_DEFAULT);
        if ($password !== $checkpassword) {
            return [
                ['message' => 'Password not match'],
                ['status' => '400']
            ];
        }
        if (strlen($password) < 8) {

            return [
                ['message' => 'Password must be at least 8 characters long'],
                ['status' => '400']
            ];
        }
        if (!preg_match('/[A-Z]/', $password))
            return [
                ['message' => 'Password must contain uppercase'],
                ['status' => '400']
            ];
        if (!preg_match('/[a-z]/', $password))
            return [
                ['message' => 'Password must contain lowercase'],
                ['status' => '400']
            ];
        if (!preg_match('/[0-9]/', $password))
            return [
                ['message' => 'Password must contain number'],
                ['status' => '400']
            ];
        if (!preg_match('/[\W_]/', $password))
            return [
                ['message' => 'Password must contain special char'],
                ['status' => '400']
            ];
        $sql = "SELECT email, Password FROM signup WHERE email = ?";
        $query = $this->conn->prepare($sql);
        $query->bind_param("s", $email);
        $query->execute();
        $result = $query->get_result();
        $row = $result->fetch_assoc();
        $oldemail = $row['email'];
         if($oldemail !== $email){
            return [
                ['message' => 'Email not found'],
                ['status' => '400']
            ];
         }
        $oldHash = $row['Password'];
        $isChanged = true;
        if (!password_verify($oldPassword, $oldHash)) {
            return [
                ['message' => 'old password is incorrect'],
                ['status' => '400']
            ];
        }
        if($password === $oldPassword){
            return [
                ['message' => 'New password cannot be the same as the old password'],
                ['status' => '400']
            ];
        }
        $sql = "UPDATE signup SET Password = ?, isChanged = ?, updated_at = ?, updated_by = ?
         WHERE email = ?";
        $query = $this->conn->prepare($sql);
        $query->bind_param("sisis", $hashpassword, $isChanged, $updated_at, $userId, $email);
        $query->execute();

        if (!$query->affected_rows > 0) {
            return [
                ['message' => 'Password updated failed'],
                ['status' => '200']
            ];
        }      
             return [
                ['message' => 'Password update Successfully'],
                ['status' => '200']
            ];       
    }
    public function registerUser(array $input)
    {

    // var_dump($input);
    // die;
        $firstname = $input['firstname'];
        $lastName = $input['lastname'];
        $email = $input['email'];
        $password = $input['password'];
        $role_Id = $input['role'];
        $bankName = $input['bankName'] ?? ' ';
      $bankAccount  = $input['bankAccount'] ?? ' ';
      
      $businessName = $input['businessName'] ?? ' ';
      $cnic   = (int)($input['cnic'] ?? '');
      $shopAddress  = $input['shopAddress'] ?? ' ';
      $taxId = (int)($input['taxId'] ?? '');



        $isChanged=true;    
        $created_at = date('Y-m-d H:i:s', time());
        $created_by = $role_Id;
        $checkpassword = $input['confirmPassword'];
        $hashpassowrd = password_hash($password, PASSWORD_DEFAULT);
        if (!empty($firstname && $lastName && $email && $password)) {
            $sql = "SELECT * FROM signup WHERE email = ?";
            $query = $this->conn->prepare($sql);
            $query->bind_param("s", $email);
            $query->execute();
            $query->store_result();
            if ($query->num_rows > 0) {
                return [['message' => 'Email already exist'], ['status' => '400']];
            }
            if ($password !== $checkpassword) {

                return [
                    ['message' => 'Password not match'],
                    ['status' => '400']
                ];
            }
            if (strlen($password) < 8) {

                return [
                    ['message' => 'Password must be at least 8 characters long'],
                    ['status' => '400']
                ];
            }
            if (!preg_match('/[A-Z]/', $password))
                return [
                    ['message' => 'Password must contain uppercase'],
                    ['status' => '400']
                ];
            if (!preg_match('/[a-z]/', $password))
                return [
                    ['message' => 'Password must contain lowercase'],
                    ['status' => '400']
                ];
            if (!preg_match('/[0-9]/', $password))
                return [
                    ['message' => 'Password must contain number'],
                    ['status' => '400']
                ];
            if (!preg_match('/[\W_]/', $password))
                return [
                    ['message' => 'Password must contain special char'],
                    ['status' => '400']
                ];
                if($role_Id !==4){  
                    $sql = "INSERT INTO signup (Firstname,Lastname,Email,Password,role_Id,created_by, created_at,isChanged) VALUES (?,?,?,?,?,?,?,?)";
            $query = $this->conn->prepare($sql);
            $query->bind_param("ssssiisi", $firstname, $lastName, $email, $hashpassowrd, $role_Id, $created_by, $created_at, $isChanged);
            $query->execute();
            if ($query->affected_rows > 0) {
                  $otp = rand(10000,99999);   
                $userId= $this->conn->insert_id;

            //   echo $_SESSION['user_Id'] . " is your user Id";
            //   die;
               
                $created_at = date('Y-m-d H:i:s', time());
                $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));    
                $sql= 'Insert into user_otps (user_Id,otp,expiry) Values (?,?,?)';
                $query= $this->conn->prepare($sql);
                $query->bind_param('iss', $userId,$otp,$expiry);
                $query->execute();
                if($query->affected_rows>0){
                    $this->emailServer->sendOTP($email,$otp);
                      return [
                ['message' => 'Data has been added successfully and verification email sent'],
                ['status' => '200'],
                ['user_Id'=> $userId],
            ];
                }
            }
             return [
                ['message' => 'Data not been added successfully'],
                ['status' => '200']
            ];
            
                }

         if (
        empty($bankName)||
    empty($bankAccount) ||
    empty($businessName) ||
    empty($cnic) ||
    empty($shopAddress) ||
    empty($taxId)
) {
    return [
        'message' => 'All fields are required for supplier registration',
        'status'  => 400
    ];
}
// var_dump($bankAccount, $businessName, $cnic, $shopAddress, $taxId);
//  die;
               $sql  = "INSERT INTO signup (Firstname,Lastname,Email,Password,role_Id,Business_Name,CNIC,bankName,Bank_account ,Shop_address,Tax_Id, created_by, created_at,isChanged) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $query = $this->conn->prepare($sql);
            $query->bind_param("ssssisisssiisi", $firstname, $lastName, $email, $hashpassowrd, $role_Id, $businessName, $cnic,$bankName, $bankAccount, $shopAddress, $taxId, $created_by, $created_at, $isChanged);
            $query->execute();
            if ($query->affected_rows > 0) {                    
                $otp = rand(10000,99999);
                // echo $otp;
                // die;    
                $userId= $this->conn->insert_id;
                 
                if (session_status() === PHP_SESSION_NONE){
                session_start();
                $_SESSION['user_Id']= $userId;    
                

                // var_dump($_SESSION);
                // die;
                }
                $created_at = date('Y-m-d H:i:s', time());
                $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));
                $this->emailServer->sendOTP($email,$otp);
                $sql= 'Insert into user_otps (user_Id,otp,expiry) Values (?,?,?)';
                $query= $this->conn->prepare($sql);
                $query->bind_param('iss', $userId,$otp,$expiry);
                $query->execute();
 
                if($query->affected_rows> 0){
              return [
                ['message' => 'Data has been added successfully and verification Email sent'],
                ['status' => '200'],
                 ['user_Id'=> $userId]
            ];
               

            }
        }
            return [
                ['message' => 'Data not  added successfully'],   
                ['status' => '200'],
               
            ];
  
    }
        return [['message' => 'All Fields are required'], ['status' => '400']];
    
    }
   public function forgotPasswordOTP($input){
    $email= $input['email'];
  $sql= 'Select email , Id from signup where email = ? And isDeleted != true;'; 
  $query= $this->conn->prepare($sql);
  $query->bind_param('s',$email);
  $query->execute();
  $result= $query->get_result();
  if($result->num_rows>0){
    $row= $result->fetch_assoc();
    $user_Id= $row['Id'];

     $otp = rand(10000,99999);
     $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));
     $sql = 'Select user_Id from user_otps where user_Id = ?';
     $query->bind_param('i',$user_Id);
     $query->execute();
     if($query->affected_rows==0){
          $result->free();
            $query->close();
     $sql= "Insert into user_otps (user_Id,forgotPasswordOtp,fotgotPasswordExpiry) values (?,?,?)";
    $query= $this->conn->prepare($sql);
    $query->bind_param('iis', $user_Id,$otp,$expiry);
    $query->execute();
    if($query->affected_rows>0){
     $this->emailServer->sendOTP($email,$otp);
    return [[
        'message'=> 'OTP has been on your email address'
    ],['status'=> '200']];
     }
     }
       $result->free();
            $query->close();
    $sql= "update user_otps Set forgotPasswordOtp =?, fotgotPasswordExpiry=? where user_Id = ?";
    $query= $this->conn->prepare($sql);
    $query->bind_param('isi', $otp,$expiry,$user_Id);
    $query->execute();
    if($query->affected_rows>0){
     $this->emailServer->sendOTP($email,$otp);
    return [[
        'message'=> 'OTP has been on your email address'
    ],['status'=> '200']];
    }

  }
   return [[
        'message'=> 'Something Went Wrong'
    ],['status'=> '400']];
 
  }
  public function verifyforgetPasswordOtp($input){
    // var_dump($input);
    // die;
    $otp = $input['otpNumber']; 
    $email = $input['email'];
    ((int)$user_Id = $this->getUser($email));
    $user_Id = $user_Id['Id'];
    $sql = "SELECT fotgotPasswordExpiry FROM user_otps WHERE user_Id = ? AND forgotPasswordOtp = ?";
    $query = $this->conn->prepare($sql);
    $query->bind_param('ii', $user_Id, $otp);
    $query->execute();

    $result = $query->get_result();
    // var_dump($result);
    // die;
    if ($result->num_rows == 0) {
        return [
            ['message' => 'Please enter the correct OTP'],
            ['status' => '400']
        ];
    }
    $row = $result->fetch_assoc();
    $expirytoken = new DateTime($row['fotgotPasswordExpiry']);
    $currentTime = new DateTime();

    if ($currentTime > $expirytoken) {
        return [
            ['message' => 'Your OTP has expired. Please request a new one.'],
           [ 'status' => '400']
        ];
    }

        return [
            ['message' => 'Email has been verified'],
           [ 'status' => '200']
        ];
    } 

    
public function verifyOtp($input){

    $otp = $input['otpNumber'];
    $user_Id = $input['user_Id'];
    $sql = "SELECT * FROM user_otps WHERE user_Id = ? AND otp = ?";
    $query = $this->conn->prepare($sql);
    $query->bind_param('is', $user_Id, $otp);
    $query->execute();

    $result = $query->get_result();
    if ($result->num_rows == 0) {
        return [
            ['message' => 'Please enter the correct OTP'],
            ['status' => '400']
        ];
    }
    $row = $result->fetch_assoc();
    $expirytoken = new DateTime($row['expiry']);
    $currentTime = new DateTime();

    if ($currentTime > $expirytoken) {
        return [
            ['message' => 'Your OTP has expired. Please request a new one.'],
           [ 'status' => '400']
        ];
    }
    $sql = 'UPDATE signup SET isEmailVerified = true WHERE Id = ?';
    $query = $this->conn->prepare($sql);
    $query->bind_param('i', $user_Id);
    $query->execute();

    if ($query->affected_rows > 0) {
        return [
            ['message' => 'Email has been verified'],
           [ 'status' => '200']
        ];
    } else {
        return [
            ['message' => 'Verification failed or already verified'],
            ['status' => '400']
        ];
    }
}
    public function addUser($input)
    {
        if (!$this->checkToken($this-> getBearerToken())) {
            return [['message' => 'Please login again'],  ["status" => "400"]];
        }
        $userId = $this->getUserId($this-> getBearerToken());
        if (!$this->hasPermission($userId, "create_user")) {
            return [
                ["message" => "you have no permission to do the process"],
                ["status" => "400"]
            ];
        }
        $firstname = $input['firstname'];
        $lastName = $input['lastname'];
        $email = $input['email'];
        $password = $this->generateTempPassword();
        $role_Id = $input['role_Id'];
        $created_at = date('Y-m-d H:i:s', time());
        $created_by = (!empty($this-> getBearerToken())) ? $this->getUserId($this-> getBearerToken()) : null;
        $isChanged=false;

        $hashpassowrd = password_hash($password, PASSWORD_DEFAULT);
        if (!empty($firstname && $lastName && $role_Id && $email && $password)) {
            $sql = "SELECT * FROM signup WHERE email = ?";
            $query = $this->conn->prepare($sql);
            $query->bind_param("s", $email);
            $query->execute();
           $result= $query->get_result();
            if ($result->num_rows > 0) {
                return [['message' => 'Email already exist'], ['status' => '400']];
            }
            $sql = "INSERT INTO signup (Firstname,Lastname,Email,Password,role_Id,created_by,created_at,isChanged) VALUES (?,?,?,?,?,?,?,?)";
            $query = $this->conn->prepare($sql);
            $query->bind_param("ssssiisi", $firstname, $lastName, $email, $hashpassowrd, $role_Id, $created_by, $created_at, $isChanged);
            $query->execute();
            if (!$query->affected_rows > 0) {
                return [
                    ['message' => 'Data not added successfully'],
                    ['status' => '400'],
                   
                ];
            }
             $this->emailServer->sendemail($email, $password);
            return [
                ['message' => 'Data has been added successfully and email sent successfully'],
                ['status' => '200'],
                 ['data' => "$password"]
            ];
        }
    }
    public function deleteUser($input)
    {
        if (!$this->checkToken($this-> getBearerToken())) {
            return [['message' => 'Please login again'],  ["status" => "400"]];
        }
        $userId = (!empty($this-> getBearerToken())) ? $this->getUserId($this-> getBearerToken()) : null;
        if (!$this->hasPermission($userId, "delete_user")) {
            return [
                ["message" => "you have no permission to do the process"],
                ["status" => "400"]
            ];
        }
        $id = $input["user_Id"];
        $update_at = date('Y-m-d H:i:s', time());

        $sql = "Update signup Set IsDeleted=true, updated_by=?, updated_at=?   WHERE Id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("isi", $userId, $update_at, $id);

        $stmt->execute();

        if ($stmt->affected_rows == 0) {
            return [
                ['message' => 'User not deleted'],
                ["status" => '400']
            ];
        }
        return [
            ['message' => 'User has been delete successfully'],
            ["status" => '200']
        ];
    }
    public function updateUser($input)
    {
        if (!$this->checkToken($this-> getBearerToken())) {
            return [['message' => 'Please login again'],  ["status" => "400"]];
        }
        $userId = $this->getUserId($this-> getBearerToken());
        if (!$this->hasPermission($userId, "update_user")) {
            return [
                ["message" => "you have no permission to do the process"],
                ["status" => "400"]
            ];
        }
        $firstname = $input['firstname'];
        $lastName = $input['lastname'];
        $email = $input['email'];
        $Id = $input['user_Id'];
        $password = $this->generateTempPassword();
        $role_Id = $input['role_Id'];
        $updated_at = date('Y-m-d H:i:s', time());
        $updated_by = (!empty($this-> getBearerToken())) ? $this->getUserId($this-> getBearerToken()) : null;
        $sql = "SELECT * FROM signup WHERE email = ?";
        $query = $this->conn->prepare($sql);
        $query->bind_param("s", $email);
        $query->execute();
        $query->store_result();
        if ($query->num_rows > 0) {
            return [['message' => 'Email already exist'], ['status' => '400']];
        }
        $sql = "update signup Set Firstname=?,Lastname=?,Email=? , role_Id=?, updated_by= ?  , updated_at=?  Where  Id=? ";
        $query = $this->conn->prepare($sql);
        $query->bind_param('sssiisi', $firstname, $lastName, $email, $role_Id, $updated_by, $updated_at, $Id);
        $query->execute();
        if (!($query->affected_rows > 0)) {
            return [
                ['message' => 'Data not updated successfully'],
                ["status" => "400"]
            ];
        }
        return [
            ['message' => 'Data has been updated successfully'],
            ["status" => "200"]
        ];
    }


public function updateProduct($input)
{
  

    if (!$this->checkToken($this->getBearerToken())) {
        return [['message' => 'Please login again'], ["status" => "400"]];
    }

    $userId = $this->getUserId($this->getBearerToken());

    if (!$this->hasPermission($userId, "update_product")) {
        return [
            ["message" => "you have no permission to do the process"],
            ["status" => "400"]
        ];
    }
    $name = $input['product_name'];
    $categoryid = $input['category_id'];
    $price = $input['price'];
    $quantity = $input['quantity'];
    $description=$input['description'];
    $oldPrice=$input['oldPrice'];
    $id = (int) $input["product_Id"];
    $update_at = date('Y-m-d H:i:s');

    $stmt = $this->conn->prepare("SELECT Image, image_Id FROM products WHERE product_Id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    $imagePath = $result['Image'] ?? null;
    $publicId  = $result['image_Id'] ?? null;

 
  if (!empty($_FILES['image']['tmp_name'])) {

    if (!empty($publicId)) {
        (new AdminApi())->deleteAssets([$publicId]);
    }
        $uploadResult = (new UploadApi())->upload(
        $_FILES['image']['tmp_name'],
        ["folder" => "products"]
    );

    $imagePath = $uploadResult['secure_url'];

    $publicId  = $uploadResult['public_id'];
}

    $sql = "UPDATE products 
            SET product_name=?, 
                 Image=?,
                 image_Id=?,
                category_id=?, 
                price=?,
                oldPrice=?, 
                quantity=?, 
                Description=?,
                updated_by=?, 
                updated_at=? 
            WHERE product_Id=?";

    $query = $this->conn->prepare($sql);

    $query->bind_param(
        'sssiiiisisi', $name,
        $imagePath,
        $publicId,
        $categoryid,
        $price,
        $oldPrice,
        $quantity,
        $description,
        $userId,
        $update_at,
        $id);
       

    $query->execute();

    if (!($query->affected_rows > 0)) {
        return [
            ['message' => 'Data not updated successfully'],
            ["status" => "400"]
        ];
    }

    return [
        ['message' => 'Data has been updated successfully'],
        ["status" => "200"]
    ];
}
    public function updateCategory($input)
    {
        if (!$this->checkToken($this-> getBearerToken())) {
            return [['message' => 'Please login again'],  ["status" => "400"]];
        }
        $userId = $this->getUserId($this-> getBearerToken());
        if (!$this->hasPermission($userId, "category_update")) {
            return [
                ["message" => "you have no permission to do the process"],
                ["status" => "400"]
            ];
        }
        $category_name = $input['category_name'];

        $id = $input["category_id"];
        $update_at = date('Y-m-d H:i:s', time());

        $sql = "update categories  Set category_name=? ,updated_by=? , updated_at=?  Where  category_id=? ";
        $query = $this->conn->prepare($sql);
        $query->bind_param('sisi', $category_name, $userId, $update_at, $id);
        $query->execute();

        if (!($query->affected_rows > 0)) {
            return [
                ['message' => 'Data not updated successfully'],
                ["status" => "400"]
            ];
        }
        return [
            ['message' => 'Data has been updated successfully'],
            ["status" => "200"]
        ];
    }
    private function checkToken($token)
    {
        $token = $this->_Jwt->decodeJwt($token);

        $expiry_date = $token[0]->exp;
        $currentTime = time();
        if ($expiry_date < $currentTime) {

            return false;
        } else {
            return true;
        }
    }
    private function  hasPermission($userId, $permission)
    {
        $sql = "Select  p.permission_name from role_permission rp 
   LEFT join signup S  ON  S.role_Id = rp.role_Id
   LEFT join permission p ON p.permission_Id = rp.permission_Id
    WHERE S.Id = ? AND p.permission_name = ? And p.IsDeleted = false AND rp.IsDeleted = false AND rp.isDeleted = false AND rp.status != true";

        $query = $this->conn->prepare($sql);
        $query->bind_param("is", $userId, $permission);
        $query->execute();

        $result = $query->get_result();

        if ($result->num_rows > 0) {
            return true;
        }
        return false;
    }


    private function generateTempPassword($length = 10)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $password;
    }
private function getEmail($user_Id){
 $sql= "Select email from signup where Id = ? ";
$query= $this->conn->prepare($sql);
$query->bind_param('i', $user_Id);
$result=$query->execute();
$result=$query->get_result();
$row= $result->fetch_assoc();
 return $row;
    }
private function getUser($email){
 $sql= "Select Id from signup where email = ? ";
$query= $this->conn->prepare($sql);
$query->bind_param('s', $email);
$result=$query->execute();
$result=$query->get_result();
$row= $result->fetch_assoc();
 return $row;
    }
    private function getBearerToken()
{
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
  
    if (!$authHeader) {
        return null;
    }
    return str_replace('Bearer ', '', $authHeader);
}
    private function getUserId($token)
    {
        // var_dump($token);
        // die;
        $token = $this->_Jwt->decodeJwt($token);

        $userId = $token[0]->{'$userId'};

        return $userId ?: 0;
    }
}
