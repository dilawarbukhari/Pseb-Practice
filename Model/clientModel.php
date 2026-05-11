<?php
require './config.php';
require './vendor/autoload.php';
use Dompdf\Dompdf;
class clientModel extends db
{
    private $_Jwt;
    private $_config;

    public function __construct()
    {

        parent::__construct();
        $this->_Jwt = new JwtHandler();
        $this->_config = new Config();
    }

    public function getReviewStatus($input)
    {
        $order_Id = $input['order_Id'];
        $sql = 'Select isSubmitted from product_reviews where order_Id=?';
        $query = $this->conn->prepare($sql);
        $query->bind_param('i', $order_Id);
        $query->execute();
        $result = $query->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $isSubmitted = $row['isSubmitted'];
            return [
                ['data' => $isSubmitted],
                ['status' => '200']
            ];
        }
    }

    public function generateSaleReport()
    {
        if (!$this->checkToken($this->getBearerToken())) {
            return [['message' => 'Please login again'], ["status" => "400"]];
        }
        $seller_Id = $this->getUserId($this->getBearerToken());
        $sql = "
SELECT  
    p.product_name,
    DATE(oi.created_at) AS sale_date,
    SUM(oi.quantity) AS totalSold,
    SUM(oi.quantity * oi.price) AS revenue FROM order_items oi  JOIN products p ON p.product_Id = oi.product_Id WHERE p.seller_Id = ? GROUP BY oi.product_Id ORDER BY totalSold DESC";
$stmt = $this->conn->prepare($sql);

$stmt->bind_param("i", $seller_Id);

$stmt->execute();

$result = $stmt->get_result();

$html = '

<h2>Seller Product Sales Report</h2>

<table border="1" width="100%" cellpadding="10">

<tr>
    <th>Product</th>
    <th>Total Sold</th>
    <th>Revenue</th>
</tr>
';

while($row = $result->fetch_assoc()){

    $html .= '

    <tr>
        <td>'.$row['product_name'].'</td>
        <td>'.$row['totalSold'].'</td>
        <td>Rs '.$row['revenue'].'</td>
    </tr>
    ';
}

$html .= '</table>';

$dompdf = new Dompdf();

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$pdfOutput = $dompdf->output();
return [
    "status" => 200,
    "fileName" => "seller-report.pdf",
    "pdfBase64" => base64_encode($pdfOutput)
];

    }
    public function trackOrder($input)
    {
        if (!$this->checkToken($this->getBearerToken())) {
            return [['message' => 'Please login again'], ["status" => "400"]];
        }
        $trackingnumber = $input['trackingNumber'];

        if (empty($trackingnumber)) {
            return [
                ['message' => 'No data Found'],
                ['status' => '400']
            ];
        }

        $sql = 'Select o.order_number,o.tracking_number,oi.created_at,oi.updated_at,oi.status_Id, s.status_name from orders o JOIN order_items oi ON  oi.order_Id= o.order_Id Join status s On s.status_Id = oi.status_Id
Where o.tracking_number =?';
        $query = $this->conn->prepare($sql);
        $query->bind_param('s', $trackingnumber);
        $query->execute();
        $result = $query->get_result();
        $data = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data = $row;
            }
            return [
                ['data' => $data],
                ['status' => '200']
            ];
        }
        return [
            ['message' => 'No data Found'],
            ['status' => '400']
        ];
    }
    public function orderPlace($input)
    {
        if (!$this->checkToken($this->getBearerToken())) {
            return [['message' => 'Please login again'], ["status" => "400"]];
        }

        $UserId = $this->getUserId($this->getBearerToken());
        $total_amount = $input['totalAmount'];
        $status = 1;
        $created_at = date('Y-m-d H:i:s', time());

        $email = $this->getEmail($UserId);

        $address = $input['ShippingDetails']['shippingAddress'];
        $city = $input['ShippingDetails']['shippingCity'];
        $postalCode = $input['ShippingDetails']['shippingPostalCode'];
        $PhoneNumber = $input['ShippingDetails']['shippingPhone'];
        // foreach ($input['items'] as $item) {
        //   $productId = $item['product_Id'];
        //     $quantity = $item['quantity'];
        // }  
        $trackingNumber = 'TRK-' . date('Y') . '-' . rand(1000, 9999);
        $sql = "INSERT INTO orders (user_id,tracking_number, total_amount, status,created_at,created_by) VALUES (?,?, ?, ?, ?, ?)";
        $query = $this->conn->prepare($sql);
        $query->bind_param('isiisi', $UserId, $trackingNumber, $total_amount, $status, $created_at, $UserId);
        $result = $query->execute();
        if ($result) {

            $orderId = $this->conn->insert_id;
            $order_number = 'ORD' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
            $updateSql = "UPDATE orders SET order_number = ? WHERE order_id = ?";
            $updateQuery = $this->conn->prepare($updateSql);
            $updateQuery->bind_param('si', $order_number, $orderId);
            $updateQuery->execute();
            if ($updateQuery->affected_rows > 0) {

                $sqlItem = "INSERT INTO order_items (order_id,product_id, quantity, price,created_at,created_by) 
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

                    $sql = "INSERT INTO order_shipping_details (order_id, address, city, postal_code, phone_number, created_at, created_by) VALUES (?, ?, ?, ?, ?,?,?)";
                    $query = $this->conn->prepare($sql);
                    $query->bind_param('isssisi', $orderId, $address, $city, $postalCode, $PhoneNumber, $created_at, $UserId);

                    $result = $query->execute();
                    if ($result) {
                        $this->_config->sendOrderConfirmation($input, $email, $order_number, $trackingNumber);
                        return [['message' => 'Order placed successfully and order number has been sent on your email address'], ['status' => 200]];
                    }
                }
            }
        } else {
            return [['message' => 'Failed to place order'], ['status' => 400]];
        }
    }

    public function getOrderDetail()
    {
        if (!$this->checkToken($this->getBearerToken())) {
            return [['message' => 'Please login again'], ["status" => "400"]];
        }
        $UserId = $this->getUserId($this->getBearerToken());

        $sql = " Select o.order_Id,o.order_number,o.user_id,oi.status_Id,s.status_Name,o.created_at,oi.product_id,oi.quantity,p.Image,p.product_name,p.category_id,p.description,c.Category_name,oi.price from orders o LEFT JOIN order_items oi ON o.order_Id = oi.order_id LEFT JOIN products p ON p.product_Id = oi.product_id LEFT JOIN categories c ON p.category_id = c.category_id  LEFT JOIN status s ON oi.status_Id = s.status_Id  Where user_Id = ? AND o.IsDeleted = false AND oi.IsDeleted = false AND p.IsDeleted = false AND c.IsDeleted = false";
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


    public function Totalrecord()
    {
        $UserId = $this->getUserId($this->getBearerToken());

        $sql = "SELECT COUNT(DISTINCT o.order_Id) AS total_orders, COUNT(DISTINCT CASE WHEN s.status_Name = 'Pending' THEN o.order_Id END) AS pending_orders, COUNT(DISTINCT CASE WHEN s.status_Name = 'Shipped' THEN o.order_Id END) AS shipped_orders, COUNT(DISTINCT CASE WHEN s.status_Name = 'Delivered' THEN o.order_Id END) AS delivered_orders FROM orders o INNER JOIN order_items oi ON o.order_Id = oi.order_Id INNER JOIN products p ON p.product_Id = oi.product_Id INNER JOIN product_users pu ON pu.product_Id = p.product_Id LEFT JOIN status s ON oi.status_Id = s.status_Id WHERE pu.user_Id = ? AND pu.isDeleted = false AND o.IsDeleted = false AND oi.isCancelled = false AND oi.IsDeleted = false AND p.IsDeleted = false;
";
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
    public function getConfirmOrder()
    {
        $UserId = $this->getUserId($this->getBearerToken());

        $sql = "SELECT o.order_Id, o.order_number, o.created_at, o.user_id AS buyer_id, oi.status_Id, s.status_Name, oi.product_id, oi.quantity, p.Image, p.product_name, p.category_id, p.description, c.Category_name, oi.price FROM orders o INNER JOIN order_items oi ON o.order_Id = oi.order_Id INNER JOIN products p ON p.product_Id = oi.product_Id INNER JOIN product_users pu ON pu.product_Id = p.product_Id LEFT JOIN categories c ON p.category_id = c.category_id LEFT JOIN status s ON oi.status_Id = s.status_Id WHERE pu.user_Id = ? AND pu.isDeleted = false AND o.IsDeleted = false AND oi.isCancelled = false AND oi.IsDeleted = false AND p.IsDeleted = false AND c.IsDeleted = false;
";
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

    public function getStatus()
    {
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
    public function Feedback($input)
    {
        $order_Id = $input['order_Id'];

        $sql = 'Select isSubmitted from product_reviews where order_Id=?';
        $query = $this->conn->prepare($sql);
        $query->bind_param('i', $order_Id);
        $query->execute();
        $result = $query->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $isSubmitted = $row['isSubmitted'];
            //  echo $isSubmitted;
//  die;
            if ($isSubmitted == 1) {
                return [
                    ['message' => 'You already submitted feedback'],
                    ['status' => '400']
                ];
            }
        }

        $rating = $input['rating'];
        $feedbackdetail = $input['feedbackdetail'];
        $isSubmitted = 1;
        $created_at = date('Y-m-d H:i:s', time());
        $sql = 'Select user_Id from orders  Where order_Id =?';
        $query = $this->conn->prepare($sql);
        $query->bind_param('i', $order_Id);
        $query->execute();
        $result = $query->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user_Id = $row['user_Id'];

            $sql = 'Insert into product_reviews(order_Id,user_Id,rating,review,isSubmitted,created_at) Values (?,?,?,?,?,?)';
            $query = $this->conn->prepare($sql);
            $query->bind_param('iiisis', $order_Id, $user_Id, $rating, $feedbackdetail, $isSubmitted, $created_at);
            $query->execute();

            if ($query->affected_rows > 0) {
                return [
                    ['message' => 'Feedback has been submitted successfully'],
                    ['status' => '200']
                ];
            }
            return [
                ['message' => 'Feedback not submitted'],
                ['status' => '400']
            ];
        }
    }



    public function updateOrderStatus($input)
    {
        $order_Id = $input['order_Id'];
        $status = (int) ($input['status_Id']);
        $product_Id = $input['product_Id'];
        $orderUserId = $this->getorderUserId($order_Id);


        session_start();
        $_SESSION['order_Id'] = $order_Id;
        $_SESSION['product_Id'] = $product_Id;
        $_SESSION['user_Id'] = $orderUserId;


        //  echo  $_SESSION['order_Id'];
        //  echo  $_SESSION['product_Id'];
        //  echo $_SESSION['user_Id'];


        $email = $this->getEmail($orderUserId);
        $orderNumber = $this->getordernumber($order_Id);
        $orderStatus = $this->getorderStatus($status);


        $UserId = $this->getUserId($this->getBearerToken());
        $Update_at = date('Y-m-d H:i:s', time());


        $sql = " Update  order_items Set status_Id = ?, updated_at = ? ,updated_by = ?  Where order_Id = ? AND product_Id = ? AND IsDeleted = false";
        $query = $this->conn->prepare($sql);
        $query->bind_param('isiii', $status, $Update_at, $UserId, $order_Id, $product_Id);
        $query->execute();
        if ($query->affected_rows > 0) {
            $this->_config->sendOrderStatusUpdate($email, $orderNumber, $orderStatus, $order_Id);
            return [['message' => 'Order status updated successfully and email has been sent'], ['status' => 200]];
        } else {
            return [['message' => 'Failed to update order status'], ['status' => 400]];
        }
    }

    public function cancelOrder($input)
    {
        $order_Id = $input['order_Id'];
        $product_Id = $input['product_Id'];
        $orderUserId = $this->getorderUserId($order_Id);

        $sql = " Update order_items Set isCancelled = true, status_Id = 4 Where order_Id = ? AND product_Id = ? AND IsDeleted = false";
        $query = $this->conn->prepare($sql);
        $query->bind_param('ii', $order_Id, $product_Id);
        $query->execute();

        if ($query->affected_rows > 0) {
            $sql = " Update order  Set status = 4 Where order_Id = ?  AND user_Id = ? AND IsDeleted = false";
            return [['message' => 'Order cancelled successfully'], ['status' => 200]];
        } else {
            return [['message' => 'Failed to cancel order'], ['status' => 400]];
        }
    }



    public function getPendingOrders()
    {
        $sql = "Select count(*) as pending_orders from orders where status = 1 AND IsDeleted = false";
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
    public function getShippedOrder()
    {
        $sql = "Select count(*) as shipped_orders from orders where status = 2 AND IsDeleted = false";
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
    public function getDeliveredOrder()
    {
        $sql = "Select count(*) as delivered_orders from orders where status = 3 AND IsDeleted = false";
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

    public function getTotalUser()
    {
        $sql = "Select count(*) as total_users from signup where IsDeleted = false ";
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
    public function getTotalProduct()
    {
        $sql = "Select count(*) as total_products from products where IsDeleted = false and isCancelled=false;";
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
    public function getTotalOrder()
    {
        $sql = "Select count(*) as total_orders from orders where IsDeleted = false  AND isCancelled=false";
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
    public function getRecentOrder()
    {
        $sql = "SELECT o.order_id,o.order_number, o.user_Id, u.Firstname , u.Lastname, o.total_amount,o.created_At, o.status, s.status_Name FROM orders o JOIN signup u ON o.user_Id = u.Id JOIN status s ON o.status = s.status_Id Where o.isCancelled = false  And o.isDeleted=false   ORDER BY o.created_at DESC LIMIT 5;";
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
    public function getThisMonth()
    {
        $sql = "SELECT COUNT(*) AS new_users FROM signup WHERE IsDeleted = false AND created_at >= DATE_FORMAT(CURRENT_DATE(), '%Y-%m-01') AND created_at < DATE_FORMAT(CURRENT_DATE() + INTERVAL 1 MONTH, '%Y-%m-01');";
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
    public function getTotalCategory()
    {
        $sql = "Select count(*) as total_category from categories where IsDeleted = false";
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
    public function getTotalRevenue()
    {
        $sql = "SELECT SUM(total_amount) AS total_revenue
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
    public function getCategoryProduct()
    {
        $sql = "SELECT c.category_id, c.category_name AS category_name, COUNT(p.product_Id) AS total_products FROM categories c LEFT JOIN products p ON p.category_id = c.category_id Where c.isDeleted =false GROUP BY c.category_id, c.category_name;";
        $query = $this->conn->prepare($sql);
        $query->execute();
        $result = $query->get_result();
        $User = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $User[] = $row;
            }
            return ['data' => $User, 'status' => 200];
        } else {
            return ['message' => 'Failed to fetch category products', 'status' => 400];
        }
    }
    public function getTopProduct()
    {
        $sql = "SELECT 
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
        $User = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $User[] = $row;
            }
            return ['data' => $User, 'status' => 200];
        } else {
            return ['message' => 'Failed to fetch top products', 'status' => 400];
        }
    }

    public function getActiveUsers()
    {
        $sql = "SELECT COUNT(*) as active_users
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
    private function hasPermission($userId, $permission)
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


    private function getEmail($userId)
    {
        $sql = "select email from signup where Id = ? AND IsDeleted = false";
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
    private function checkToken($token)
    {
        $token = $this->_Jwt->decodeJwt($token);

        $expiry_date = $token[0]->exp;
        $currentTime = time();
        if ($expiry_date < $currentTime) {

            return false;
        } else {
            return true;
        }
    }
}
