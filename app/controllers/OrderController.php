<?php
require_once 'app/models/CartModel.php';
require_once 'app/models/OrderModel.php';
require_once 'app/models/ProductModel.php';
require_once 'app/config/database.php';

class OrderController
{
    private $cartModel;
    private $orderModel;
    private $productModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->cartModel = new CartModel();
        $this->orderModel = new OrderModel();
        $this->productModel = new ProductModel($this->db);
    }

    public function checkout()
    {
        $items = $this->cartModel->getCartProducts($this->productModel);
        if (empty($items)) {
            header('Location: /Cart/index');
            exit;
        }
        include 'app/views/order/checkout.php';
    }

    public function processCheckout()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = htmlspecialchars(strip_tags($_POST['name']));
            $phone = htmlspecialchars(strip_tags($_POST['phone']));
            $address = htmlspecialchars(strip_tags($_POST['address']));
            $payment_method = $_POST['payment_method'] ?? 'COD';

            $items = $this->cartModel->getCartProducts($this->productModel);
            $totalAmount = $this->cartModel->getTotalAmount($this->productModel);

            if (empty($items)) {
                die('Giỏ hàng trống!');
            }

            $orderData = [
                'user_id' => 1, 
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
                'total' => $totalAmount,
                'payment_method' => $payment_method,
                'status' => 'Pending' 
            ];

            $orderId = $this->orderModel->createOrder($orderData);

            foreach ($items as $item) {
                $detailItem = [
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['product']->price
                ];
                $this->orderModel->addOrderDetail($orderId, $detailItem);
            }

            if ($payment_method === 'COD') {
                $this->cartModel->clearCart(); 
                header('Location: /Order/orderConfirmation');
                exit;
            } elseif ($payment_method === 'VNPAY') {
                $this->vnpayPayment($totalAmount, $orderId);
            }
        }
    }

    private function vnpayPayment($totalAmount, $orderId)
    {
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $vnp_TmnCode = "FJ1S4GTU"; 
        $vnp_HashSecret = "IS00TZQPKFXYTW3FZXC2XUZXGHYJK913"; 
        
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
   
        $vnp_Returnurl = "http://localhost/Order/vnpayReturn"; 

        $vnp_TxnRef = $orderId; 
        $vnp_OrderInfo = "Thanh toan don hang " . $orderId;
        $vnp_OrderType = "billpayment";
        $vnp_Amount = $totalAmount * 100; 
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        );

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        header('Location: ' . $vnp_Url);
        die();
    }

    public function vnpayReturn()
    {
        if (isset($_GET['vnp_ResponseCode']) && $_GET['vnp_ResponseCode'] == '00') {
            $orderId = $_GET['vnp_TxnRef'];
            
            $this->orderModel->updateStatus($orderId, 'Paid');
            $this->cartModel->clearCart();
            
            header('Location: /Order/orderConfirmation');
            exit;
        } else {
            echo "<h2>Giao dịch thất bại hoặc đã bị hủy!</h2>";
            echo "<a href='/Cart/index'>Quay lại giỏ hàng</a>"; 
        }
    }

    public function orderConfirmation()
    {
        include 'app/views/order/orderConfirmation.php';
    }
}
?>