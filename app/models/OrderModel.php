<?php

require_once 'app/config/database.php';

class OrderModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = (new Database())->getConnection();
    }

    public function createOrder($data)
    {
        $sql = "INSERT INTO orders (user_id, total, payment_method, status)
                VALUES (:user_id, :total, :payment_method, :status)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':total', $data['total']);
        $stmt->bindParam(':payment_method', $data['payment_method']);
        $stmt->bindParam(':status', $data['status']);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    public function addOrderDetail($orderId, $item)
    {
        $sql = "INSERT INTO order_details (order_id, product_id, quantity, price)
                VALUES (:order_id, :product_id, :quantity, :price)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':order_id', $orderId);
        $stmt->bindParam(':product_id', $item['product_id']);
        $stmt->bindParam(':quantity', $item['quantity']);
        $stmt->bindParam(':price', $item['price']);

        $stmt->execute();
    }

    public function updateStatus($orderId, $status)
    {
        $sql = "UPDATE orders SET status = :status WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $orderId);

        $stmt->execute();
    }
}