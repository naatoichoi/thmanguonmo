<?php

require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';
require_once 'app/models/CartModel.php';

class ProductController
{
    private $productModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    public function index()
    {
        $this->list();
    }

    public function list()
    {
        $products = $this->productModel->getProducts();
        include 'app/views/product/list.php';
    }

    public function show($id)
    {
        $product = $this->productModel->getProductById($id);
        $images = $this->productModel->getProductImages($id);

        if ($product) {
            include 'app/views/product/show.php';
        } else {
            echo "Không tìm thấy sản phẩm.";
        }
    }

    public function add()
    {
        $categories = (new CategoryModel($this->db))->getCategories();
        include 'app/views/product/add.php';
    }
    //mở form thêm sản phẩm
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $category_id = $_POST['category_id'] ?? null;

            // Upload ảnh chính
            $image = $this->uploadImage('image');

            // Thêm sản phẩm vào database
            $result = $this->productModel->addProduct($name, $description, $price, $category_id, $image);

            if (is_array($result)) {
                $errors = $result;
                $categories = (new CategoryModel($this->db))->getCategories();
                include 'app/views/product/add.php';
                return;
            }

            if ($result) {
                $product_id = $result;

                // Upload nhiều ảnh phụ
                if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                    $this->uploadMultipleImages($product_id);
                }

                header('Location: /Product/list');
                exit();
            }

            echo "Đã xảy ra lỗi khi thêm sản phẩm.";
        }
    }

    public function edit($id)
    {
        $product = $this->productModel->getProductById($id);
        $categories = (new CategoryModel($this->db))->getCategories();
        $images = $this->productModel->getProductImages($id);

        if ($product) {
            include 'app/views/product/edit.php';
        } else {
            echo "Không tìm thấy sản phẩm.";
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? null;
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $category_id = $_POST['category_id'] ?? null;

            $image = null;

            // Nếu có chọn ảnh chính mới thì upload ảnh mới
            if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
                $image = $this->uploadImage('image');
            }

            $edit = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image);

            if ($edit) {
                // Nếu có chọn thêm ảnh phụ mới thì thêm vào ảnh cũ
                if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                    $this->uploadMultipleImages($id);
                }

                header('Location: /Product/show/' . $id);
                exit();
            }

            echo "Đã xảy ra lỗi khi lưu sản phẩm.";
        }
    }

    public function delete($id)
    {
        if ($this->productModel->deleteProduct($id)) {
            header('Location: /Product/list');
            exit();
        }

        echo "Đã xảy ra lỗi khi xóa sản phẩm.";
    }

    public function deleteImage($id, $product_id)
    {
        if ($this->productModel->deleteProductImage($id)) {
            header('Location: /Product/edit/' . $product_id);
            exit();
        }

        echo "Đã xảy ra lỗi khi xóa ảnh.";
    }

    private function uploadImage($inputName)
    {
        if (!isset($_FILES[$inputName])) {
            return null;
        }

        if ($_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if (empty($_FILES[$inputName]['name'])) {
            return null;
        }

        $projectRoot = dirname(__DIR__, 2);
        $uploadDir = $projectRoot . '/assets/images/products/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $originalName = basename($_FILES[$inputName]['name']);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'jfif'];
        if (!in_array($extension, $allowedTypes)) {
            return null;
        }

        $fileName = time() . '_' . uniqid() . '.' . $extension;
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $targetFile)) {
            return $fileName;
        }

        return null;
    }


    private function uploadMultipleImages($product_id)
    {
        if (!isset($_FILES['images'])) {
            return;
        }

        $projectRoot = dirname(__DIR__, 2);
        $uploadDir = $projectRoot . '/assets/images/products/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'jfif'];

        foreach ($_FILES['images']['name'] as $key => $name) {
            if (empty($name)) {
                continue;
            }

            if ($_FILES['images']['error'][$key] !== UPLOAD_ERR_OK) {
                continue;
            }

            $originalName = basename($name);
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            if (!in_array($extension, $allowedTypes)) {
                continue;
            }

            $fileName = time() . '_' . uniqid() . '_' . $key . '.' . $extension;
            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $targetFile)) {
                $this->productModel->addProductImage($product_id, $fileName);
            }
        }
    }
}

?>