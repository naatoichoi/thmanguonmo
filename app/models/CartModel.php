<?php

require_once 'app/config/redis.php';

class CartModel
{
    private $redis;
    private $userId = 1;

    public function __construct()
    {
        $this->redis = RedisConnection::getConnection();
    }

    private function getCartKey()
    {
        return "cart:" . $this->userId;
    }

    public function addToCart($productId, $quantity = 1)
    {
        return $this->redis->hincrby(
            $this->getCartKey(),
            $productId,
            $quantity
        );
    }

    public function getCart()
    {
        $cart = $this->redis->hgetall($this->getCartKey()) ?? [];

        foreach ($cart as $k => $v) {
            $cart[$k] = (int)$v;
        }

        return $cart;
    }

    public function updateQuantity($productId, $quantity)
    {
        if ($quantity <= 0) {
            return $this->removeItem($productId);
        }

        return $this->redis->hset(
            $this->getCartKey(),
            $productId,
            (int)$quantity
        );
    }

    public function removeItem($productId)
    {
        return $this->redis->hdel(
            $this->getCartKey(),
            $productId
        );
    }

    public function clearCart()
    {
        return $this->redis->del(
            $this->getCartKey()
        );
    }

    public function getCartProducts($productModel)
    {
        $cart = $this->getCart();

        $items = [];

        foreach ($cart as $productId => $quantity) {

            $product = $productModel->getProductById($productId);

            if ($product) {
                $price = (float)$product->price;

                $items[] = [
                    'product'  => $product,
                    'quantity' => (int)$quantity,
                    'subtotal' => $price * (int)$quantity
                ];
            }
        }

        return $items;
    }

    public function getTotalAmount($productModel)
    {
        $items = $this->getCartProducts($productModel);

        $total = 0;

        foreach ($items as $item) {
            $total += $item['subtotal'];
        }

        return $total;
    }
}