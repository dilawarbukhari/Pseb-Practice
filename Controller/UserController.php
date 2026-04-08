<?php
require './Model/Model.php';

class Controller extends Model
{
    public function simplifyLogic($input){
      $process= $input['process'];
      switch ($process){
         case 'registerUser':
          $data = $this->registerUser($input);
          return json_encode($data);
          break;
           case 'deletePermission':
          $data = $this->deletePermission($input);
          return json_encode($data);
          break;
             case 'updatePermission':
          $data = $this->updatePermission($input);
          return json_encode($data);
          break;
             case 'addPermission':
          $data = $this->addPermission($input);
          return json_encode($data);
          break;
           case 'addUser':
          $data = $this->addUser($input);
          return json_encode($data);
          break;
          case 'addRole':
           $data = $this->addRole($input);
          return json_encode($data);
          break;
            case 'updateRolePermission':
          $data = $this->updateRolePermission($input);
          return json_encode($data);
          break;
            case 'changePassword':
          $data = $this->changePassword($input);
          return json_encode($data);
          break;
           case 'updateUserDetail':
          $data = $this->updateUserDetail($input);
          return json_encode($data);
          break;
           case 'getUserDetail':
          $data = $this->getUserDetail($input);
          return json_encode($data);
          break;
          case 'deleteRole':
          $data = $this->deleteRole($input);
          return json_encode($data);
          break;
          case 'getRolePermission':
          $data = $this->getRolePermission($input);
          return json_encode($data);
          break;
          case 'updateRole':
          $data = $this->updateRole($input);
          return json_encode($data);
          break;
           case 'updateUser':
          $data = $this->updateUser($input);
          return json_encode($data);
          break;
           case 'getAllCategory':  
        $data = $this->getAllCategory($input);
        return json_encode($data);
        break;
          case 'getAllUser':  
        $data = $this->getAllUser($input);
        return json_encode($data);
        break;
         case 'getAllPermission':  
        $data = $this->getAllPermission($input);
        return json_encode($data);
        break;
          case 'getRoles':  
        $data = $this->getAllroles($input);
        return json_encode($data);
        break;
        case 'getAllProduct':  
        $data = $this->getAllProduct($input);
        return json_encode($data);
        break;
           case 'deleteCategory':
          $data = $this->deleteCategory($input);
          return json_encode($data);
          break;
           case 'deleteUser':
          $data = $this->deleteUser($input);
          return json_encode($data);
          break;
           case 'deleteProduct':
          $data = $this->deleteProduct($input);
          return json_encode($data);
          break;
          case 'loginUser':
             $data = $this->login($input);
          return json_encode($data);
          break;
          case 'addCategory':
          $data = $this->addCategory($input);
          return json_encode($data);
          break;
          case 'addProduct':
          $data = $this->addProduct($input);
          return json_encode($data);
          break;
         case 'updateCategory':
          $data = $this->updateCategory($input);
          return json_encode($data);
          break;
             case 'updateProduct':
          $data = $this->updateProduct($input);
          return json_encode($data);
          break;
   } 
}
}
