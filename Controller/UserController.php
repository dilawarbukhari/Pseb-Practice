<?php
require './Model/Model.php';

class Controller extends Model
{
  public function handleProcess($process,$id)
  {
    switch ($process) {
      case 'getAllUser':
        $data = $this->getAll();
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
}
