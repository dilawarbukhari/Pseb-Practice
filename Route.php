<?php

// Allow requests from any origin
header("Access-Control-Allow-Origin: *");

// Allow specific headers (like JSON)
header("Access-Control-Allow-Headers: Content-Type");

// Allow specific methods
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

// Set content type to JSON
header("Content-Type: application/json");
require ('./Controller/UserController.php');
$input = json_decode(file_get_contents("php://input"), true);
$process = $input['process'] ?? '';
$id = $input['userId'] ?? null;

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
}
?>