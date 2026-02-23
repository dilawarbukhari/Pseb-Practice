<?php

header("Access-Control-Allow-Origin: *");

header("Access-Control-Allow-Headers: Content-Type");

header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

header("Content-Type: application/json");
require ('./Controller/UserController.php');
$input = json_decode(file_get_contents("php://input"), true);
$process = $input['process'] ?? '';
$id = $input['userId'] ?? null;
$name=$input['Name'] ?? null;
$email=$input['Email'] ?? null;
$phone=$input['Phone'] ?? null;
$address=$input['Address'] ?? null;
$created=$input['Created'] ?? null;

$controller = new Controller();
switch($process){  
    case 'getAllUser':
      $response= $controller->handleProcess($process,$id);
      echo $response;
     break;
    case 'deleteUser':
      $response= $controller->handleProcess($process,$id);
      echo $response;
     break;
     case 'addUser':
      $response= $controller->addLogic($process,$id,$name,$email,$phone,$address,$created);
      echo $response;
     break;
      case 'updateUser':
      $response= $controller->addLogic($process,$id,$name,$email,$phone,$address,$created);
      echo $response;
     break;
}
?>