<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';
require_once 'app/helpers/SessionHelper.php';
require_once 'app/utils/JWTHandler.php';

class AdminController {
    private $productModel;
    private $db;
    private $jwtHandler;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
        $this->jwtHandler = new JWTHandler();
    }

  private function isAdmin()
{
    SessionHelper::start();

    return isset($_SESSION['role'])
        && $_SESSION['role'] === 'admin';
}

    public function product($action = 'list') {
        if (!$this->isAdmin()) {
            http_response_code(403);
            echo "<script>alert('Bạn không có quyền truy cập!'); window.location.href='/Product/list';</script>";
            exit;
        }

        if ($action === 'list') {
            $this->productList();
        } elseif ($action === 'add') {
            $this->productAdd();
        } elseif ($action === 'edit') {
            $id = $_GET['id'] ?? null;
            if ($id) {
                $this->productEdit($id);
            }
        } else {
            $this->productList();
        }
    }

    private function productList() {
        $products = $this->productModel->getProducts();
        include 'app/views/admin/product/list.php'; 
    }

    private function productAdd() {
        $categories = (new CategoryModel($this->db))->getCategories();
        include 'app/views/admin/product/add.php';
    }

private function productEdit($id) {

    $editId = $id;

    $product = $this->productModel->getProductById($id);

    if (!$product) {
        echo "Sản phẩm không tồn tại";
        exit;
    }

    $categories = (new CategoryModel($this->db))->getCategories();

    include 'app/views/admin/product/edit.php';
}
}
?>
