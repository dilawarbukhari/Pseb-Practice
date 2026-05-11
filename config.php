<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require_once $_SERVER['DOCUMENT_ROOT'] . '/MVC/vendor/autoload.php';

class Config
{ 


    private function getMailer()
    {
        $mail = new PHPMailer(true);

        // Server Settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';         // Real SMTP server
        $mail->SMTPAuth   = true;                     // Authentication required
        $mail->Username   = 'csab7987@gmail.com';   // Your real email
        $mail->Password   = 'ueeg mgkz ucwm jnem';      // Your Google App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;                      // Standard TLS port

        $mail->setFrom('csab7987@gmail.com', 'Bukhari Mart');

        return $mail;
    }

    public function sendOrderConfirmation($input, $email, $order_number,$trackingNumber)
{
    try {
        $mail = $this->getMailer();
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Bukhari Mart - Order Confirmation #' . $order_number;

        // Extract shipping details
        $address = $input['ShippingDetails']['shippingAddress'];
        $city = $input['ShippingDetails']['shippingCity'];
        $postalCode = $input['ShippingDetails']['shippingPostalCode'];
        $phone = $input['ShippingDetails']['shippingPhone'];

        // Simple stylish HTML
        $body = '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto; border:1px solid #eee; padding:20px;">
            
            <h2 style="color:#2c3e50; text-align:center;">Order Confirmation</h2>
            
            <p>Hi,</p>
            <p>Thank you for your order! Your order has been received successfully.</p>

            <div style="background:#f7f7f7; padding:15px; border-radius:5px;">
                <p><strong>Order Number:</strong> #' . $order_number . '</p>
                     <p><strong>Tracking Number:</strong>' . $trackingNumber . '</p>
                <p><strong>Order Date:</strong> ' . date('F j, Y, g:i a') . '</p>
            
<p><strong>Payment Method:</strong> ' . ucwords(str_replace('_', ' ', "Credit Card")) . '</p>
            </div>

            <h3 style="margin-top:20px;">Shipping Details</h3>
            <p>
                ' . htmlspecialchars($address) . '<br>
                ' . htmlspecialchars($city) . ' - ' . htmlspecialchars($postalCode) . '<br>
                Phone: ' . htmlspecialchars($phone) . '
            </p>

            <h3>Order Summary</h3>
            <table width="100%" cellpadding="10" style="border-collapse: collapse;">
                <tr style="background:#2c3e50; color:#fff;">
                    <th align="left">Product</th>
                    <th align="center">Qty</th>
                    <th align="right">Price</th>
                </tr>';

        // Loop items (make sure you pass $input['items'])
        if (!empty($input['items'])) {
            foreach ($input['items'] as $item) {
                $body .= '
                <tr style="border-bottom:1px solid #ddd;">
                    <td>' . htmlspecialchars($item['product_name']) . '</td>
                    <td align="center">' . $item['cartitem'] . '</td>
                    <td align="right">Rs ' . number_format($item['price'], 2) . '</td>
                </tr>';
            }
        }

        $body .= '
            </table>

            <p style="margin-top:20px;">
<strong>Total Amount with 5% Tax:</strong> Rs ' . number_format($input['totalAmount'], 2) . ' <br>
                <strong>Status:</strong> Pending <br>
                <strong>Payment:</strong> ' . ucfirst("Credit Card") . '
            </p>

            <hr>
            <p style="text-align:center;">
                Thank you for shopping with <strong>Bukhari Mart</strong> ❤️
            </p>

        </div>';

        $mail->Body = $body;

        return $mail->send();

    } catch (Exception $e) {
        error_log("Order confirmation email failed: " . $mail->ErrorInfo);
        return false;
    }
}

public function sendOrderStatusUpdate($email, $order_number, $status, $orderId)
{
    try {
        $mail = $this->getMailer();
        $mail->addAddress($email);
        $mail->isHTML(true);

        // Subject based on status
        $mail->Subject = 'Bukhari Mart - Order #' . $order_number . ' is ' . ucfirst($status);

        // Status message
        $message = '';
        if ($status === 'Shipped') {
            $message = 'Your order has been shipped and is on the way 🚚';
        } elseif ($status === 'Delivered') {
            $message = 'Your order has been delivered successfully 🎉';
        } else {
            $message = 'Your order status has been updated.';
        }

        // Email Body
        $body = '
        <div style="font-family: Arial, sans-serif; max-width:600px; margin:auto; border:1px solid #eee; padding:20px;">
            
            <h2 style="text-align:center; color:#2c3e50;">Order Update</h2>

            <p>Hi,</p>
            <p>' . $message . '</p>

            <div style="background:#f7f7f7; padding:15px; border-radius:5px; margin-top:15px;">
                <p><strong>Order Number:</strong> #' . $order_number . '</p>
                <p><strong>Status:</strong> ' . ucfirst($status) . '</p>
                <p><strong>Updated On:</strong> ' . date('F j, Y, g:i a') . '</p>
            </div>';

        // Extra UI for delivered
        if ($status === 'Delivered') {
           $feedbackUrl =  'http://localhost:4200/feedback/' . $orderId;

    $body .= '
    <p style="margin-top:20px;">
        We hope you enjoy your purchase ❤️ <br>
        Thank you for shopping with <strong>Bukhari Mart</strong>.
    </p>

    <!-- ⭐ Feedback Section -->
    <div style="margin-top:25px; padding:20px; background:#fff8e1; border:1px solid #ffe0b2; border-radius:8px; text-align:center;">
        
        <h3 style="margin-bottom:10px;">⭐ How was your experience?</h3>
        <p style="font-size:14px; color:#555;">Your feedback helps us improve our service</p>

        <!-- Quick Rating -->
        <div style="margin:15px 0;">
            <a href="'.$feedbackUrl.'&rating=5" style="text-decoration:none; font-size:22px;">
                ⭐⭐⭐⭐⭐
            </a>
        </div>

        <!-- Review Button -->
        <a href="'.$feedbackUrl.'" 
           style="display:inline-block; margin-top:10px; padding:10px 20px; background:#28a745; color:#fff; text-decoration:none; border-radius:5px;">
           Write a Review
        </a>

    </div>';
        }

        // Extra UI for shipped
        if ($status === 'Shipped') {
            $body .= '
            <p style="margin-top:20px;">
                Your package is on the way. Please keep your phone available for delivery 📦
            </p>';
        }

        $body .= '
            <hr>
            <p style="text-align:center;">Need help? Contact our support team anytime.</p>
        </div>';

        $mail->Body = $body;

        return $mail->send();

    } catch (Exception $e) {
        error_log("Status update email failed: " . $mail->ErrorInfo);
        return false;
    }
}


public function sendOTP($email, $otp)
    {
        try {
            $mail = $this->getMailer();
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your Verification Code';

$mail->Body = "
<div style='font-family: Arial, sans-serif; background-color: #f4f6f8; padding: 20px;'>
    <div style='max-width: 500px; margin: auto; background: #ffffff; border-radius: 10px; padding: 30px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);'>
        
        <h2 style='color: #333;'>Verification Code</h2>
        
        <p style='color: #555; font-size: 16px;'>
            Use the following One-Time Password (OTP) to complete your verification:
        </p>
        
        <div style='font-size: 28px; font-weight: bold; letter-spacing: 5px; color: #2c7be5; margin: 20px 0;'>
            $otp
        </div>
        
        <p style='color: #888; font-size: 14px;'>
            This code will expire in <b>15 minutes</b>.
        </p>

        <hr style='margin: 20px 0;'>

        <p style='font-size: 12px; color: #aaa;'>
            If you did not request this code, please ignore this email.
        </p>
    </div>
</div>
";

            return $mail->send();
        } catch (Exception $e) {
            error_log("Email failed: " . $mail->ErrorInfo);
            return false;
        }
    }
public function sendemail($email, $password)
{
    try {
        $mail = $this->getMailer();
        $mail->addAddress($email);
        $mail->isHTML(true);

        // Subject based on status
        $mail->Subject = 'Account Created - Bukhari Mart';

        // Status message
      $message = 'Your account has been successfully created 🎉';

        // Email Body
      $body = '
<div style="font-family: Arial, sans-serif; max-width:600px; margin:auto; border:1px solid #eee; padding:20px;">
    
    <h2 style="text-align:center; color:#2c3e50;">Welcome to Bukhari Mart 🎉</h2>

    <p>Hi,</p>
    <p>Your account has been successfully created.</p>

    <div style="background:#f7f7f7; padding:15px; border-radius:5px; margin-top:15px;">
        <p><strong>Email:</strong> ' . $email . '</p>
        <p><strong>Password:</strong> ' . $password . '</p>
        <p><strong>Created On:</strong> ' . date('F j, Y, g:i a') . '</p>
    </div>

    <p style="margin-top:20px;">
        You can now login and start using your account.<br>
        For security, please change your password after login 🔒
    </p>

    <div style="text-align:center; margin-top:25px;">
        <a href="http://yourwebsite.com/login" 
           style="background:#3498db; color:#fff; padding:10px 20px; text-decoration:none; border-radius:5px;">
           Login Now
        </a>
    </div>

    <hr>
    <p style="text-align:center;">
        Need help? Contact our support team anytime.
    </p>

    <p style="text-align:center;">
        ❤️ Thank you for joining <strong>Bukhari Mart</strong>
    </p>
</div>';

        $mail->Body = $body;

        return $mail->send();

    } catch (Exception $e) {
        error_log("Status update email failed: " . $mail->ErrorInfo);
        return false;
    }
}
}