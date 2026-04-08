<?php

header("Access-Control-Allow-Origin: http://localhost:4200");

header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");
require('./Controller/UserController.php');
$input = json_decode(file_get_contents("php://input"), true);
$process= $input['process'];

$controller = new Controller();
switch ($process) {
  case 'getAllCategory':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
    case 'getRolePermission':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
     case 'updateRolePermission':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
     case 'getAllProduct':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
    case 'getRoles':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
    case 'updateRole':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
    case 'deleteRole':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
     case 'getAllPermission':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
     case 'getAllUser':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
     case 'updatePermission':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
    case 'deletePermission':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
    case 'addPermission':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
    case 'addUser':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
    case 'changePassword':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
     case 'getUserDetail':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
       case 'updateUserDetail':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
     case 'updateUser':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
  case 'registerUser':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
  case 'loginUser':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
  case 'deleteCategory':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
    case 'deleteUser':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
    case 'addRole':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
    case 'deleteProduct':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
  case 'addCategory':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
  case 'addProduct':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
  case 'updateCategory':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
      case 'updateProduct':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
}
?>