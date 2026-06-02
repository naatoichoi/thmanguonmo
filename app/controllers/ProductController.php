<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';
require_once 'app/helpers/SessionHelper.php';

class ProductController {
    private $productModel;
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    private function isAdmin() {
        return SessionHelper::isAdmin();
    }

    public function index() {
        $products = $this->productModel->getProducts();
        include 'app/views/product/list.php';
    }

    public function list() {
        $this->index();
    }

    public function show($id) {
        $product = $this->productModel->getProductById($id);
        if ($product) {
            include 'app/views/product/show.php';
        } else {
            echo "Không thấy sản phẩm.";
        }
    }

    public function add() {
        if (!$this->isAdmin()) {
            echo "<script>alert('Bạn không có quyền truy cập chức năng này!'); window.location.href='/Product/list';</script>";
            exit;
        }
        $categories = (new CategoryModel($this->db))->getCategories();
        include_once 'app/views/product/add.php';
    }

    public function save() {
        if (!$this->isAdmin()) {
            echo "<script>alert('Bạn không có quyền truy cập chức năng này!'); window.location.href='/Product/list';</script>";
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $category_id = $_POST['category_id'] ?? null;
            
            $image = (isset($_FILES['image']) && $_FILES['image']['error'] == 0) 
                     ? $this->uploadImage($_FILES['image']) 
                     : "";

            $result = $this->productModel->addProduct($name, $description, $price, $category_id, $image);
            
            if (is_array($result)) {
                $errors = $result;
                $categories = (new CategoryModel($this->db))->getCategories();
                include 'app/views/product/add.php';
            } else {
                header('Location: /Product/list');
            }
        }
    }

    public function edit($id) {
        if (!$this->isAdmin()) {
            echo "<script>alert('Bạn không có quyền truy cập chức năng này!'); window.location.href='/Product/list';</script>";
            exit;
        }
        $product = $this->productModel->getProductById($id);
        $categories = (new CategoryModel($this->db))->getCategories();
        if ($product) {
            include 'app/views/product/edit.php';
        } else {
            echo "Không thấy sản phẩm.";
        }
    }

    public function update() {
        if (!$this->isAdmin()) {
            echo "<script>alert('Bạn không có quyền truy cập chức năng này!'); window.location.href='/Product/list';</script>";
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $category_id = $_POST['category_id'];
            
            $image = (isset($_FILES['image']) && $_FILES['image']['error'] == 0) 
                     ? $this->uploadImage($_FILES['image']) 
                     : $_POST['existing_image'];

            $edit = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image);
            
            if ($edit) {
                header('Location: /Product/list');
            } else {
                echo "Đã xảy ra lỗi khi lưu sản phẩm.";
            }
        }
    }

    public function delete($id) {
        if (!$this->isAdmin()) {
            echo "<script>alert('Bạn không có quyền truy cập chức năng này!'); window.location.href='/Product/list';</script>";
            exit;
        }
        if ($this->productModel->deleteProduct($id)) {
            header('Location: /Product/list');
        } else {
            echo "Đã xảy ra lỗi khi xóa sản phẩm.";
        }
    }

    private function uploadImage($file) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($file["name"]);
        move_uploaded_file($file["tmp_name"], $target_file);
        return "/" . $target_file;
    }
}
?>