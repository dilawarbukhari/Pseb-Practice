<?php
require './config.php';
class clientModel extends db{
        private $_Jwt;
        private $_config;

       public function __construct()
    {

        parent::__construct();
        $this->_Jwt = new JwtHandler();
        $this->_config = new Config();
    }

    public function orderPlace($input){

    $UserId = $this->getUserId($this->getBearerToken());
    $total_amount = $input['totalAmount'];
    $status = 1;
    $created_at = date('Y-m-d H:i:s',time());

     $email = $this->getEmail($UserId);

    $address = $input['ShippingDetails']['shippingAddress'];
    $city = $input['ShippingDetails']['shippingCity'];
    $postalCode = $input['ShippingDetails']['shippingPostalCode'];
    $PhoneNumber = $input['ShippingDetails']['shippingPhone'];
    // foreach ($input['items'] as $item) {
    //   $productId = $item['product_Id'];
    //     $quantity = $item['quantity'];
    // }  
    $sql = "INSERT INTO orders (user_id, total_amount, status,created_at,created_by) VALUES (?, ?, ?, ?, ?)";
    $query = $this->conn->prepare($sql);
    $query->bind_param('iiisi',$UserId, $total_amount, $status, $created_at, $UserId);
    $result = $query->execute();
    if ($result) {

         $orderId = $this->conn->insert_id;
         $order_number = 'ORD' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
        $updateSql = "UPDATE orders SET order_number = ? WHERE order_id = ?";
$updateQuery = $this->conn->prepare($updateSql);
$updateQuery->bind_param('si', $order_number, $orderId);
$updateQuery->execute();
         if ($updateQuery->affected_rows > 0) {
    $sqlItem = "INSERT INTO order_items (order_id, product_id, quantity, price,created_at,created_by) 
            VALUES (?, ?, ?, ?,?,?)";

$queryItem = $this->conn->prepare($sqlItem);

foreach ($input['items'] as $item) {
    $productId = $item['product_Id'];
    $quantity = $item['cartitem'];
    $price = $item['price'];

    $queryItem->bind_param('iiidsi', $orderId, $productId, $quantity, $price, $created_at, $UserId);
    $queryItem->execute();
}
if ($queryItem->affected_rows > 0) {

 $sql="INSERT INTO order_shipping_details (order_id, address, city, postal_code, phone_number, created_at, created_by) VALUES (?, ?, ?, ?, ?,?,?)";
 $query= $this->conn->prepare($sql);
    $query->bind_param('isssisi', $orderId, $address, $city, $postalCode, $PhoneNumber, $created_at, $UserId);

    $result = $query->execute();
    if ($result) {
        $this->_config->sendOrderConfirmation($input, $email, $order_number);
        return [['message' => 'Order placed successfully and order number has been sent on your email address'],['status' => 200]];
    } 
    }}}else {
        return [ ['message' => 'Failed to place order'],['status' => 400]];
    }
    }

 public function getOrderDetail(){
    $UserId = $this->getUserId($this->getBearerToken());

    $sql = " Select o.order_Id,o.order_number,o.user_id,o.status,s.status_Name,o.created_at,oi.product_id,oi.quantity,p.Image,p.product_name,p.category_id,p.description,c.Category_name,oi.price from orders o LEFT JOIN order_items oi ON o.order_Id = oi.order_id LEFT JOIN products p ON p.product_Id = oi.product_id LEFT JOIN categories c ON p.category_id = c.category_id  LEFT JOIN status s ON o.status = s.status_Id  Where user_Id = ? AND o.IsDeleted = false AND oi.IsDeleted = false And o.isCancelled=false AND p.IsDeleted = false AND c.IsDeleted = false";
    $query = $this->conn->prepare($sql);
    $query->bind_param('i', $UserId);
    $query->execute();
    $result = $query->get_result();
    if ($result->num_rows > 0) {
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        return ['data' => $orders, 'status' => 200];
    } else {
        return ['message' => 'No orders found', 'status' => 404];
    }
 } 
 
 public function getConfirmOrder(){
    $UserId = $this->getUserId($this->getBearerToken());

    $sql = " Select o.order_Id,o.order_number,o.created_at,o.user_id,o.status,s.status_Name,o.created_at,oi.product_id,oi.quantity,p.Image,p.product_name,p.category_id,p.description,c.Category_name,oi.price from orders o LEFT JOIN order_items oi ON o.order_Id = oi.order_id LEFT JOIN products p ON p.product_Id = oi.product_id LEFT JOIN categories c ON p.category_id = c.category_id  LEFT JOIN status s ON o.status = s.status_Id  Where  o.IsDeleted = false AND o.isCancelled = false AND oi.IsDeleted = false AND p.IsDeleted = false AND c.IsDeleted = false";
    $query = $this->conn->prepare($sql);
    $query->execute();
    $result = $query->get_result();
    if ($result->num_rows > 0) {
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        return ['data' => $orders, 'status' => 200];
    } else {
        return ['message' => 'No orders found', 'status' => 404];
    }
 }  

  public function getStatus(){
    // $UserId = $this->getUserId($this->getBearerToken());
$sql = "SELECT * FROM status";
$query = $this->conn->prepare($sql);
$query->execute();
    $result = $query->get_result();
    if ($result->num_rows > 0) {
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        return ['data' => $orders, 'status' => 200];
    } else {
        return ['message' => 'No status found', 'status' => 400];
    }
 }  
public function updateOrderStatus($input){
$order_Id = $input['order_Id'];
$status = (int)($input['status_Id']);
$orderUserId= $this->getorderUserId($order_Id);


$email = $this->getEmail($orderUserId); 
$orderNumber = $this->getordernumber($order_Id);
$orderStatus = $this->getorderStatus($status);


$UserId = $this->getUserId($this->getBearerToken());
$Update_at= date('Y-m-d H:i:s',time());


$sql = " Update orders Set status = ?, updated_at = ? ,updated_by = ?  Where order_Id = ? AND user_id = ? AND IsDeleted = false";
$query = $this->conn->prepare($sql);
$query->bind_param('isiii', $status, $Update_at, $UserId, $order_Id, $orderUserId);
$query->execute();
// var_dump($query);
// die;
if ($query->affected_rows > 0) {
$this->_config->sendOrderStatusUpdate($email, $orderNumber, $orderStatus);
    return [['message' => 'Order status updated successfully and email has been sent'],['status' => 200]];
} else {
    return [['message' => 'Failed to update order status'],['status' => 400]];
}
}

public function cancelOrder($input){
$order_Id = $input['order_Id'];
$orderUserId= $this->getorderUserId($order_Id);

$sql = " Update orders Set status = 4 ,isCancelled = true Where order_Id = ? AND user_Id = ? AND IsDeleted = false";
$query = $this->conn->prepare($sql);
$query->bind_param('ii', $order_Id, $orderUserId);
$query->execute();

if ($query->affected_rows > 0) {
    return [['message' => 'Order cancelled successfully'],['status' => 200]];
} else {
    return [['message' => 'Failed to cancel order'],['status' => 400]];
}
}

public function getPendingOrders(){

$sql= "Select count(*) as pending_orders from orders where status = 1 AND IsDeleted = false";
$query = $this->conn->prepare($sql);
$query->execute();
$result = $query->get_result();
if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    return ['data' => $data, 'status' => 200];
} else {
    return ['message' => 'Failed to fetch pending orders', 'status' => 400];
}
}
public function getShippedOrder(){
$sql= "Select count(*) as shipped_orders from orders where status = 2 AND IsDeleted = false";
$query = $this->conn->prepare($sql);
$query->execute();
$result = $query->get_result();
if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    return ['data' => $data, 'status' => 200];
} else {
    return ['message' => 'Failed to fetch shipped orders', 'status' => 400];
}
}
public function getDeliveredOrder(){
$sql= "Select count(*) as delivered_orders from orders where status = 3 AND IsDeleted = false";
$query = $this->conn->prepare($sql);
$query->execute();
$result = $query->get_result();
if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    return ['data' => $data, 'status' => 200];
} else {
    return ['message' => 'Failed to fetch delivered orders', 'status' => 400];
}
}

public function getTotalUser(){
$sql= "Select count(*) as total_users from signup where IsDeleted = false ";
$query = $this->conn->prepare($sql);
$query->execute();
$result = $query->get_result();
if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    return ['data' => $data, 'status' => 200];
} else {
    return ['message' => 'Failed to fetch total users', 'status' => 400];
}
}
public function getTotalProduct(){
$sql= "Select count(*) as total_products from products where IsDeleted = false";
$query = $this->conn->prepare($sql);
$query->execute();
$result = $query->get_result();
if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    return ['data' => $data, 'status' => 200];
} else {
    return ['message' => 'Failed to fetch total products', 'status' => 400];
}
}
public function getTotalOrder(){
$sql= "Select count(*) as total_orders from orders where IsDeleted = false";
$query = $this->conn->prepare($sql);
$query->execute();
$result = $query->get_result();
if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    return ['data' => $data, 'status' => 200];
} else {
    return ['message' => 'Failed to fetch total orders', 'status' => 400];
}
}
public function getRecentOrder(){
$sql="SELECT o.order_id,o.order_number, o.user_Id, u.Firstname , u.Lastname, o.total_amount,o.created_At, o.status, s.status_Name FROM orders o JOIN signup u ON o.user_Id = u.Id JOIN status s ON o.status = s.status_Id Where o.isCancelled = false  And o.isDeleted=false   ORDER BY o.created_at DESC LIMIT 5;";
$query = $this->conn->prepare($sql);
$query->execute();
$result = $query->get_result();
if ($result->num_rows > 0) {
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    return ['data' => $orders, 'status' => 200];
} else {
    return ['message' => 'No recent orders found', 'status' => 404];
}
}
public function getThisMonth(){
  $sql= "SELECT COUNT(*) AS new_users FROM signup WHERE IsDeleted = false AND created_at >= DATE_FORMAT(CURRENT_DATE(), '%Y-%m-01') AND created_at < DATE_FORMAT(CURRENT_DATE() + INTERVAL 1 MONTH, '%Y-%m-01');";
$query = $this->conn->prepare($sql);
$query->execute();
$result = $query->get_result();
if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    return ['data' => $data, 'status' => 200];
} else {
    return ['message' => 'Failed to fetch new users this month', 'status' => 400];
}
}
public function getTotalCategory(){
$sql= "Select count(*) as total_category from categories where IsDeleted = false";
$query = $this->conn->prepare($sql);
$query->execute();
$result = $query->get_result();
if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    return ['data' => $data, 'status' => 200];
} else {
    return ['message' => 'Failed to fetch total category', 'status' => 400];
}
}
public function getTotalRevenue(){
    $sql= "SELECT SUM(total_amount) AS total_revenue
FROM orders
WHERE IsDeleted = false;";
    $query = $this->conn->prepare($sql);
    $query->execute();
    $result = $query->get_result();
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        return ['data' => $data, 'status' => 200];
    } else {
        return ['message' => 'Failed to fetch total revenue', 'status' => 400];
    }
}
public function getCategoryProduct(){
    $sql="SELECT c.category_id, c.category_name AS category_name, COUNT(p.product_Id) AS total_products FROM categories c LEFT JOIN products p ON p.category_id = c.category_id Where c.isDeleted =false GROUP BY c.category_id, c.category_name;";
    $query = $this->conn->prepare($sql);
    $query->execute();
    $result = $query->get_result();
    $User=[];
    if ($result->num_rows > 0) {
        while($row= $result->fetch_assoc()){
            $User[]=$row;
        }
        return ['data' => $User, 'status' => 200];
    } else {
        return ['message' => 'Failed to fetch category products', 'status' => 400];
    }
}
public function getTopProduct(){
    $sql="SELECT 
    p.product_Id,
    p.product_name,
        c.category_name,
    SUM(oi.quantity) AS total_sold
FROM order_items oi
JOIN products p ON oi.product_id = p.product_Id
Join Categories c ON c.category_Id= p.category_Id
JOIN orders o ON oi.order_id = o.order_id
WHERE o.IsDeleted = false
AND o.status = '3'
GROUP BY p.product_Id, p.product_name,c.category_name
ORDER BY total_sold DESC
LIMIT 5";
    $query = $this->conn->prepare($sql);
    $query->execute();
    $result = $query->get_result();
    $User=[];
    if ($result->num_rows > 0) {
        while($row= $result->fetch_assoc()){
            $User[]=$row;
        }
        return ['data' => $User, 'status' => 200];
    } else {
        return ['message' => 'Failed to fetch top products', 'status' => 400];
    }
}
public function getActiveUsers(){
$sql= "SELECT COUNT(*) as active_users
FROM signup 
WHERE IsDeleted = false 
AND last_login >= NOW() - INTERVAL 30 DAY;";
$query = $this->conn->prepare($sql);
$query->execute();
$result = $query->get_result();
if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    return ['data' => $data, 'status' => 200];
} else {
    return ['message' => 'Failed to fetch active users', 'status' => 400];
}
}
    private function  hasPermission($userId, $permission)
    {
        $sql = "Select  p.permission_name from role_permission rp 
   LEFT join signup S  ON  S.role_Id = rp.role_Id
   LEFT join permission p ON p.permission_Id = rp.permission_Id
    WHERE S.Id = ? AND p.permission_name = ? And p.IsDeleted = false AND rp.IsDeleted = false AND rp.isDeleted = false AND rp.status != true";

        $query = $this->conn->prepare($sql);
        $query->bind_param("is", $userId, $permission);
        $query->execute();

        $result = $query->get_result();

        if ($result->num_rows > 0) {
            return true;
        }
        return false;
    }
    private function generateTempPassword($length = 10)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $password;
    }
    private function getBearerToken()
{
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
  
    if (!$authHeader) {
        return null;
    }
    return str_replace('Bearer ', '', $authHeader);
}
    private function getUserId($token)
    {
        // var_dump($token);
        // die;
        $token = $this->_Jwt->decodeJwt($token);

        $userId = $token[0]->{'$userId'};

        return $userId ?: 0;
    }

    private function getorderUserId($orderId)
    {
        $sql = "SELECT user_id FROM orders WHERE order_id = ? AND IsDeleted = false";
        $query = $this->conn->prepare($sql);
        $query->bind_param('i', $orderId);
        $query->execute();
        $result = $query->get_result();
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            return $data['user_id'];
        } else {
            return null;
        }
    }
     private function getordernumber($orderId)
    {
        $sql = "SELECT order_number FROM orders WHERE order_id = ? AND IsDeleted = false";
        $query = $this->conn->prepare($sql);
        $query->bind_param('i', $orderId);
        $query->execute();
        $result = $query->get_result();
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            return $data['order_number'];
        } else {
            return null;
        }
    }
     private function getorderStatus($status)
    {
        $sql = "SELECT status_Name FROM status  WHERE status_Id = ?";
        $query = $this->conn->prepare($sql);
        $query->bind_param('i', $status);
        $query->execute();
        $result = $query->get_result();
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            return $data['status_Name'];
        } else {
            return null;
        }
    }
    

    private function getEmail($userId){
        $sql= "select email from signup where Id = ? AND IsDeleted = false";
        $query = $this->conn->prepare($sql);
        $query->bind_param('i', $userId);
        $query->execute();
        $result = $query->get_result();
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            return $data['email'];
        } else {
            return null;
        }

    }
}

?>