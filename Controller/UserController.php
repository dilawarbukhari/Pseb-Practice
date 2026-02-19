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
}
