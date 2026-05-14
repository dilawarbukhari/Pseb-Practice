<?php

header("Access-Control-Allow-Origin: http://localhost:4200");

header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");
header("Content-Type: multipart/form-data");

require('./Controller/UserController.php');
require('./Controller/clientController.php');
$contentType = $_SERVER["CONTENT_TYPE"] ?? '';
        //  var_dump( $contentType);
if (strpos($contentType, "application/json") !== false) {
    $input = json_decode(file_get_contents("php://input"), true);
   
     
} elseif (strpos($contentType, "multipart/form-data") !== false) {
    $input = isset($_POST['data']) 
        ? json_decode($_POST['data'], true) 
        : $_POST;
    $file = $_FILES['image'] ?? null;
}

$process= $input['process'];
$controller = new Controller();
$client = new clientController();
switch ($process){
  case 'ResendforgotOTP':
     $response = $controller->simplifyLogic($input);
    echo $response;
    break;
   case 'ForgotPassword':
     $response = $controller->simplifyLogic($input);
    echo $response;
    break;
  case 'verifyforgetPasswordOtp':
     $response = $controller->simplifyLogic($input);
    echo $response;
    break;
  case 'forgotPasswordOTP':
     $response = $controller->simplifyLogic($input);
    echo $response;
    break;
case 'generateSpecificSaleReport':
         $response = $client->checkCondition($input);
    echo $response;
    break;
case 'generateYearlySaleReport':
         $response = $client->checkCondition($input);
    echo $response;
    break;
case 'generateMonthlySaleReport':
         $response = $client->checkCondition($input);
    echo $response;
    break;
  case 'generateWeeklySaleReport':
         $response = $client->checkCondition($input);
    echo $response;
    break;
  case 'generateSaleReport':
         $response = $client->checkCondition($input);
    echo $response;
    break;
  case 'getReviewStatus':
        $response = $client->checkCondition($input);
    echo $response;
    break;
  case 'Feedback':
      $response = $client->checkCondition($input);
    echo $response;
    break;
  case 'ResendOTP':
     $response= $controller->simplifyLogic($input);
    echo $response;
    break;
  case 'verifyOtp':
    $response= $controller->simplifyLogic($input);
    echo $response;
    break;
  case 'Totalrecord':
   $response = $client->checkCondition($input);
    echo $response;
    break;
  case 'getBuyerProduct':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
    case 'trackOrder':
       $response = $client->checkCondition($input);
    echo $response;
    break;  
case 'cancelOrder':
    $response = $client->checkCondition($input);
    echo $response;
    break;
  case 'getCategoryProduct':
    $response = $client->checkCondition($input);
    echo $response;
    break;
  case 'getTopProduct':
    $response = $client->checkCondition($input);
    echo $response;
    break;
  case 'getTotalRevenue':
    $response = $client->checkCondition($input);
    echo $response;
    break;
  case 'getRecentOrder':
    $response = $client->checkCondition($input);
    echo $response;
    break;
  case 'getThisMonth':
    $response = $client->checkCondition($input);
    echo $response;
    break;
  case 'getActiveUsers':
    $response = $client->checkCondition($input);
    echo $response;
    break;
  case 'getTotalUser':
    $response = $client->checkCondition($input);
    echo $response;
    break;
    case 'getTotalProduct':
    $response = $client->checkCondition($input);
    echo $response;
    break;
    case 'getTotalOrder':
    $response = $client->checkCondition($input);
    echo $response;
    break;  
    case 'getTotalCategory':
    $response = $client->checkCondition($input);
    echo $response;
    break;
      case 'getPendingOrders':
    $response = $client->checkCondition($input);
    echo $response;
    break;
         case 'getShippedOrder':
    $response = $client->checkCondition($input);
    echo $response;
    break;
  case 'getDeliveredOrder':
    $response = $client->checkCondition($input);
    echo $response;
    break;
  case 'getAllCategory':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
  case 'orderPlace':
    $response = $client->checkCondition($input);
    echo $response;
    break;

  case 'getOrderDetail':
    $response = $client->checkCondition($input);
    echo $response;
    break;
    case 'updateOrderStatus':
    $response = $client->checkCondition($input);
    echo $response;
    break;
     case 'getStatus':
    $response = $client->checkCondition($input);
    echo $response;
    break;
  case 'getConfirmOrder':
    $response = $client->checkCondition($input);
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
    $response = $controller->simplifyLogic($input,$file);
    echo $response;
    break;
  case 'updateCategory':
    $response = $controller->simplifyLogic($input);
    echo $response;
    break;
      case 'updateProduct':
    $response = $controller->simplifyLogic($input,$file=null);
    echo $response;
    break;

}
?>