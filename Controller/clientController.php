<?php        
require './Model/clientModel.php';
class clientController extends clientModel{

public function checkCondition($input){
    // var_dump($input);
    // die;
    $process=$input['process'];
    switch($process){
        case 'generateSaleReport':
          $data=$this->generateSaleReport();
            return json_encode($data);
            break;  
        case 'getReviewStatus':
              $data=$this->getReviewStatus($input);
            return json_encode($data);
            break;
        case 'Feedback':
            $data=$this->Feedback($input);
            return json_encode($data);
            break;    
        case 'trackOrder':
           $data=$this->trackOrder($input);
            return json_encode($data);
            break;  
          case 'Totalrecord':
             $data=$this->Totalrecord();
            return json_encode($data);
            break;
        case 'cancelOrder':
            $data=$this->cancelOrder($input);
            return json_encode($data);
            break;
     case 'getCategoryProduct':
            $data=$this->getCategoryProduct();
            return json_encode($data);
            break;
        case 'getTopProduct':
            $data=$this->getTopProduct();
            return json_encode($data);
            break;
        case 'getTotalRevenue':
            $data=$this->getTotalRevenue();
            return json_encode($data);
            break;
        case 'getRecentOrder':
            $data=$this->getRecentOrder();
            return json_encode($data);
            break;
        case 'getThisMonth':
            $data=$this->getThisMonth();
            return json_encode($data);
            break;
        case 'getActiveUsers':
            $data=$this->getActiveUsers();
            return json_encode($data);
            break;
        case 'getTotalUser':
            $data=$this->getTotalUser();
            return json_encode($data);
            break;
        case 'getTotalProduct':
            $data=$this->getTotalProduct();
            return json_encode($data);
            break;
        case 'getTotalOrder':
            $data=$this->getTotalOrder();
            return json_encode($data);
            break;
        case 'getTotalCategory':
            $data=$this->getTotalCategory();
            return json_encode($data);
            break;
        case 'orderPlace':
            $data=$this->orderPlace($input);
            return json_encode($data);
            break;
        case 'getOrderDetail':
            $data=$this->getOrderDetail();
            return json_encode($data);
            break;
        case 'getConfirmOrder':
            $data=$this->getConfirmOrder();
            return json_encode($data);
            break;
           
            case 'getStatus':
            $data=$this->getStatus();
            return json_encode($data);
            break;
            case 'updateOrderStatus':
            $data=$this->updateOrderStatus($input);
            return json_encode($data);
            break;
      
            case 'getPendingOrders':
            $data=$this->getPendingOrders();
            return json_encode($data);
            break;
                case 'getShippedOrder':
            $data=$this->getShippedOrder();
            return json_encode($data);
            break;
                case 'getDeliveredOrder':
            $data=$this->getDeliveredOrder();
            return json_encode($data);
            break;

    }
}

}

?>