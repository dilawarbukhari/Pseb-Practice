<?php        
require './Model/clientModel.php';
class clientController extends clientModel{

public function checkCondition($input){
    // var_dump($input);
    // die;
    $process=$input['process'];
    switch($process){
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