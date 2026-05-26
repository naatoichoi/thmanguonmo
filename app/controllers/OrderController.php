<?php

require_once 'app/models/CartModel.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/OrderModel.php';
require_once 'app/config/database.php';

class OrderController
{
    private $cartModel;
    private $productModel;
    private $orderModel;

    public function __construct()
    {
        session_start();

        $this->cartModel = new CartModel();
        $this->productModel = new ProductModel((new Database())->getConnection());
        $this->orderModel = new OrderModel();
    }

    // COD CHECKOUT
    public function checkoutCOD()
    {
        $userId = 1;

        $items = $this->cartModel->getCartProducts($this->productModel);

        if (empty($items)) {
            echo "empty";
            return;
        }

        $total = $this->cartModel->getTotalAmount($this->productModel);

        $orderId = $this->orderModel->createOrder([
            'user_id' => $userId,
            'total' => $total,
            'payment_method' => 'COD',
            'status' => 'pending'
        ]);

        foreach ($items as $item) {
            $this->orderModel->addOrderDetail($orderId, [
                'product_id' => $item['product']->id,
                'quantity'   => $item['quantity'],
                'price'      => $item['product']->price
            ]);
        }

        $this->cartModel->clearCart();

        echo "success";
    }

    // MOMO CREATE PAYMENT (FINAL FIX)
    public function createMomo()
{
    $userId = 1;

    $items = $this->cartModel->getCartProducts($this->productModel);

    if (empty($items)) {
        die("Giỏ hàng trống");
    }

    // FIX AMOUNT
    $amount = (int)$this->cartModel->getTotalAmount($this->productModel);

    if ($amount < 1000) {
        die("MoMo yêu cầu tối thiểu 1000 VND");
    }

    $amount = (string)$amount;

    // create order DB
    $orderId = $this->orderModel->createOrder([
        'user_id' => $userId,
        'total' => $amount,
        'payment_method' => 'MOMO',
        'status' => 'pending'
    ]);

    foreach ($items as $item) {
        $this->orderModel->addOrderDetail($orderId, [
            'product_id' => $item['product']->id,
            'quantity'   => $item['quantity'],
            'price'      => $item['product']->price
        ]);
    }

    $_SESSION['pending_order'] = $orderId;

    
    $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

    $partnerCode = "MOMO";
    $accessKey   = "F8B6I9A6Y9G9";
    $secretKey   = "g276077a763a42cbc7b83d3276f0c2d3";

    $orderInfo = "ThanhToanDonHang_" . $orderId;

    $requestId = (string)(time() . rand(100,999));
    $orderIdMomo = $orderId . "_" . $requestId;

    $baseUrl = "http://0a2ee78a90c6f9.lhr.life";

    $redirectUrl = $baseUrl . "/order/momoReturn";
    $ipnUrl      = $baseUrl . "/order/momoReturn";

    $requestType = "captureWallet";
    $extraData = "";

    $rawHash = "accessKey=" . $accessKey .
               "&amount=" . $amount .
               "&extraData=" . $extraData .
               "&ipnUrl=" . $ipnUrl .
               "&orderId=" . $orderIdMomo .
               "&orderInfo=" . $orderInfo .
               "&partnerCode=" . $partnerCode .
               "&redirectUrl=" . $redirectUrl .
               "&requestId=" . $requestId .
               "&requestType=" . $requestType;

    $signature = hash_hmac("sha256", $rawHash, $secretKey);

    $data = [
        "partnerCode" => $partnerCode,
        "requestId"   => $requestId,
        "amount"      => $amount,
        "orderId"     => $orderIdMomo,
        "orderInfo"   => $orderInfo,
        "redirectUrl" => $redirectUrl,
        "ipnUrl"      => $ipnUrl,
        "requestType" => $requestType,
        "extraData"   => $extraData,
        "lang"        => "vi",
        "signature"   => $signature
    ];

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);

    $result = curl_exec($ch);
    curl_close($ch);

    $json = json_decode($result, true);

    if (!isset($json['payUrl'])) {
        echo "<pre>";
        print_r($json);
        echo "</pre>";
        die("MoMo FAIL - check signature / URL");
    }

    header("Location: " . $json['payUrl']);
    exit();
}


    // MOMO RETURN
    public function momoReturn()
    {
        $orderId = $_SESSION['pending_order'] ?? null;

        if (!$orderId) {
            die("Invalid session");
        }

        $resultCode = $_GET['resultCode'] ?? -1;

        if ($resultCode == 0) {

            $this->orderModel->updateStatus($orderId, "paid");

            $this->cartModel->clearCart();

            unset($_SESSION['pending_order']);

            echo "🎉 Thanh toán MoMo thành công!";
            return;
        }

        echo "❌ Thanh toán thất bại!";
    }
}