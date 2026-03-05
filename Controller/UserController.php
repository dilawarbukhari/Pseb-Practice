<?php
require './Model/Model.php';

class Controller extends Model
{
  public function handleProcess($process,$id,$accessToken)
  {
    switch ($process) {
      case 'getAllUser':
      
        $data = $this->getAll($accessToken);
        return json_encode($data);
         case 'deleteUser':
          $data = $this->deleteUser($id);
          return json_encode($data);
      default:
        return json_encode(['message' => 'Invalid controller process']);
    }
  }
  public function addLogic($process,$id,$name,$email,$phone,$address,$created){
   switch ($process){    
  case 'addUser':
          $data = $this->addUser($name,$email,$phone,$address,$created);
          return json_encode($data);
          break;
            case 'updateUser':
          $data = $this->updateUser($id,$name,$email,$phone,$address,$created);
          return json_encode($data);
          break;
  }
  }
    public function simplifyLogic($input){
      $process= $input['process'];
      switch ($process){
         case 'registerUser':
          $data = $this->registerUser($input);
          return json_encode($data);
          break;
          case 'loginUser':
             $data = $this->login($input);
          return json_encode($data);
          break;
   } 
}
}
