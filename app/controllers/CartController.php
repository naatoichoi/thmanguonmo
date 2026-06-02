<?php

require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CartModel.php';
require_once 'app/helpers/SessionHelper.php';

class CartController
{
    private $productModel;
    private $cartModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
        $this->cartModel = new CartModel();
    }

    public function index()
    {
        if (!SessionHelper::isLoggedIn()) {
            header('Location: /Account/login');
            exit;
        }

        $items = $this->cartModel->getCartProducts($this->productModel);
        $total = $this->cartModel->getTotalAmount($this->productModel);

        include 'app/views/cart/index.php';
    }

    public function add($productId)
    {
        if (!SessionHelper::isLoggedIn()) {
            header('Location: /Account/login');
            exit;
        }

        $productId = (int)$productId;
        $product = $this->productModel->getProductById($productId);

        if (!$product) {
            die('Sản phẩm không tồn tại');
        }

        $this->cartModel->addToCart($productId, 1);

        header('Location: /Cart/index');
        exit();
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = (int)($_POST['product_id'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 1);

            if ($productId <= 0) {
                header('Location: /Cart/index');
                exit();
            }

            if ($quantity < 1) {
                $quantity = 1;
            }

            $this->cartModel->updateQuantity($productId, $quantity);
        }

        header('Location: /Cart/index');
        exit();
    }

    public function remove($productId)
    {
        $productId = (int)$productId;

        if ($productId > 0) {
            $this->cartModel->removeItem($productId);
        }

        header('Location: /Cart/index');
        exit();
    }

    public function clear()
    {
        $this->cartModel->clearCart();

        header('Location: /Cart/index');
        exit();
    }
}
?>