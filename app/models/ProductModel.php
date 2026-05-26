<?php

class ProductModel
{
    private $conn;
    private $table_name = "product";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy danh sách sản phẩm
    public function getProducts()
    {
        $query = "SELECT p.id, p.name, p.description, p.price, p.image, c.name AS category_name
                  FROM " . $this->table_name . " p
                  LEFT JOIN category c ON p.category_id = c.id
                  ORDER BY p.id ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Lấy 1 sản phẩm theo ID
    public function getProductById($id)
    {
        $query = "SELECT p.*, c.name AS category_name
                  FROM " . $this->table_name . " p
                  LEFT JOIN category c ON p.category_id = c.id
                  WHERE p.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Lấy nhiều ảnh phụ của sản phẩm
    public function getProductImages($product_id)
    {
        $query = "SELECT * FROM product_image
                  WHERE product_id = :product_id
                  ORDER BY id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Thêm sản phẩm, có ảnh chính
    public function addProduct($name, $description, $price, $category_id, $image = null)
    {
        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Tên sản phẩm không được để trống';
        }

        if (empty($description)) {
            $errors['description'] = 'Mô tả không được để trống';
        }

        if (!is_numeric($price) || $price < 0) {
            $errors['price'] = 'Giá sản phẩm không hợp lệ';
        }

        if (empty($category_id)) {
            $errors['category_id'] = 'Vui lòng chọn danh mục';
        }

        if (count($errors) > 0) {
            return $errors;
        }

        $query = "INSERT INTO " . $this->table_name . "
                  (name, description, price, image, category_id)
                  VALUES (:name, :description, :price, :image, :category_id)";

        $stmt = $this->conn->prepare($query);

        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));
        $price = htmlspecialchars(strip_tags($price));
        $category_id = htmlspecialchars(strip_tags($category_id));

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':category_id', $category_id);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }

        return false;
    }

    // Sửa sản phẩm
    public function updateProduct($id, $name, $description, $price, $category_id, $image = null)
    {
        if ($image) {
            $query = "UPDATE " . $this->table_name . "
                      SET name = :name,
                          description = :description,
                          price = :price,
                          image = :image,
                          category_id = :category_id
                      WHERE id = :id";
        } else {
            $query = "UPDATE " . $this->table_name . "
                      SET name = :name,
                          description = :description,
                          price = :price,
                          category_id = :category_id
                      WHERE id = :id";
        }

        $stmt = $this->conn->prepare($query);

        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));
        $price = htmlspecialchars(strip_tags($price));
        $category_id = htmlspecialchars(strip_tags($category_id));

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);

        if ($image) {
            $stmt->bindParam(':image', $image);
        }

        return $stmt->execute();
    }

    // Xóa sản phẩm
    public function deleteProduct($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    // Thêm ảnh phụ cho sản phẩm
    public function addProductImage($product_id, $image)
    {
        $query = "INSERT INTO product_image (product_id, image)
                  VALUES (:product_id, :image)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':image', $image);

        return $stmt->execute();
    }

    // Xóa 1 ảnh phụ
    public function deleteProductImage($image_id)
    {
        $query = "DELETE FROM product_image WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $image_id);

        return $stmt->execute();
    }
}

?>